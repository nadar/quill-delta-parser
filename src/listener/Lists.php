<?php

namespace nadar\quill\listener;

use Exception;
use nadar\quill\BlockListener;
use nadar\quill\Lexer;
use nadar\quill\Line;
use nadar\quill\Pick;

/**
 * Convert List elements (ul, ol) into Block element.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Lists extends BlockListener
{
    /**
     * @var string
     */
    public const ATTRIBUTE_LIST = 'list';

    /**
     * @var string
     */
    public const LIST_TYPE_BULLET = 'bullet';

    /**
     * @var string
     */
    public const LIST_TYPE_ORDERED = 'ordered';

    /**
     * @var string
     */
    public const LIST_TYPE_CHECKED = 'checked';

    /**
     * @var string
     */
    public const LIST_TYPE_UNCHECKED = 'unchecked';

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $listType = $line->getAttribute(self::ATTRIBUTE_LIST);
        if ($listType) {
            $this->pick($line, ['type' => $listType]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $isOpen = false;
        $isEmpty = false;
        $listTag = null;
        foreach ($this->picks() as $pick) {
            $first = $this->getFirstLine($pick);

            // defines whether this attribute list element is the last one of a list serie.
            $isLast = false;

            // if this is an empty line .... the first attribute contains the list information, otherwise
            // the first line contains content.
            if ($first->getAttribute(self::ATTRIBUTE_LIST)) {
                $isEmpty = true;
            }

            // while from first to the pick line and store content in buffer
            $buffer = null;
            if (!$isEmpty) {
                $first->while(static function (&$index, Line $line) use (&$buffer, $pick) {
                    ++$index;
                    $buffer.= $line->getInput();
                    $line->setDone();
                    if ($index == $pick->line->getIndex()) {
                        return false;
                    }
                });
            }

            // go to the next element with endlinew and check if it contains a list type until then
            $hasNextInside = false;
            $pick->line->whileNext(static function (Line $line) use (&$hasNextInside) {
                // we found the next list elemnt, stop thie while loop
                if ($line->getAttribute(self::ATTRIBUTE_LIST)) {
                    return false;
                }

                // if one of those new lines contains a endnew line or newline or is block level store this information
                if ($line->hasEndNewline() || $line->hasNewline() || ($line->isJsonInsert() && !$line->isInline())) {
                    $hasNextInside = true;
                }
            });

            // There was a newline element until next list element, so end of list has reached.
            if ($hasNextInside) {
                $isLast = true;
            }

            $output = null;

            // this makes sure that when two list types are after each other (OL and UL)
            // the previous will be closed so the new one will open
            if ($isOpen && $listTag && $listTag !== $this->getListAttribute($pick)) {
                $output .= '</'.$listTag.'>'.PHP_EOL;
                $isOpen = false;
            }

            // defines whether this attribute list element is a checked/unchecked list or not.
            $isCheck = in_array($this->getPickType($pick), [self::LIST_TYPE_CHECKED, self::LIST_TYPE_UNCHECKED], true);

            // create the opening OL/UL tag
            // opening tag process has been simplified, see https://github.com/nadar/quill-delta-parser/pull/33
            // and https://github.com/nadar/quill-delta-parser/issues/30
            if (!$isOpen) {
                $output .= '<'.$this->getListAttribute($pick);
                if ($isCheck) {
                    $output .= ' class="list-unstyled"';
                }

                $output .= '>'.PHP_EOL;
                $isOpen = true;
            }

            // when the next line has a higher intened, add nested list
            $nextIndent = 0;
            $pick->line->whileNext(static function (Line $line) use (&$nextIndent) {
                $indent = $line->getAttribute('indent', 0);
                if ($line->getAttribute(self::ATTRIBUTE_LIST)) {
                    $nextIndent = $indent;
                    return false;
                }
            });

            if ($isEmpty) {
                $output .= '<li></li>';
            } else {
                $output .= '<li>';

                if ($isCheck) {
                    $output .= '<input type="checkbox" disabled';
                    if ($this->getPickType($pick) === self::LIST_TYPE_CHECKED) {
                        $output .= ' checked';
                    }

                    $output .= '><label>'.$buffer.'</label>';
                } else {
                    $output .= $buffer;
                }

                if ($nextIndent > $pick->line->getAttribute('indent', 0)) {
                    $output .= '<'.$this->getListAttribute($pick).'>'.PHP_EOL;
                } elseif ($nextIndent < $pick->line->getAttribute('indent', 0)) {
                    $output .= '</li></'.$this->getListAttribute($pick).'></li>'.PHP_EOL;
                    $closeGap = $pick->line->getAttribute('indent', 0) - $nextIndent;
                    if ($closeGap > 1) {
                        $output .= '</'.$this->getListAttribute($pick).'></li>'.PHP_EOL;
                    }
                } else {
                    $output.= '</li>'.PHP_EOL;
                }
            }

            // close the opening OL/UL tag if:
            //   a. its the last element and the tag is opened.
            //   b. or its the last element in the picked list.
            if ($isLast || $pick->isLast()) {
                $output .= '</'.$this->getListAttribute($pick).'>'.PHP_EOL;
                $isOpen = false;
            }

            // store the last list type into a variable to determine if type switches
            $listTag = $this->getListAttribute($pick);

            $pick->line->output = $output;
            $pick->line->setDone();
        }
    }

    /**
     * Get the html tag for the given value.
     *
     * @return string
     * @throws Exception for unknown list types {@since 1.2.0}
     */
    protected function getListAttribute(Pick $pick)
    {
        $type = $this->getPickType($pick);

        if ($type === self::LIST_TYPE_ORDERED) {
            return 'ol';
        }

        if (in_array($type, [self::LIST_TYPE_BULLET, self::LIST_TYPE_CHECKED, self::LIST_TYPE_UNCHECKED], true)) {
            return 'ul';
        }

        // prevent html injection in case the attribute is user input
        throw new Exception('The provided list type "'.$type.'" is not a known list type (ordered or bullet).');
    }

    /**
     * Get the list type for the given value.
     */
    protected function getPickType(Pick $pick): string
    {
        $optionValueType = $pick->optionValue('type');
        return is_array($optionValueType) ? $optionValueType['type'] : $optionValueType;
    }
}
