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
     * @var string Class to apply on <ul> for checklists
     */
    public $checkListClass = 'list-unstyled';

    /**
     * @var array<string> Supported alignments for list items.
     */
    public $alignments = ['center', 'right', 'justify', 'left'];

    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $listType = $line->getAttribute(self::ATTRIBUTE_LIST);
        if ($listType) {
            $this->pick($line, ['type' => $listType, 'alignment' => $line->getAttribute('align')]);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        // the list tags (ol/ul) which are currently open, one entry per indent level
        /** @var array<int, string> $openLists */
        $openLists = [];
        // whether the deepest open list has an open list item
        $isItemOpen = false;
        foreach ($this->picks() as $pick) {
            $first = $this->getFirstLine($pick);

            // defines whether this attribute list element is the last one of a list serie.
            $isLast = false;

            // if this is an empty line .... the first attribute contains the list information, otherwise
            // the first line contains content. This is evaluated for every list element.
            $isEmpty = (bool) $first->getAttribute(self::ATTRIBUTE_LIST);

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
            $listTag = $this->getListAttribute($pick);
            $indent = max(0, (int) $pick->line->getAttribute('indent', 0));

            // defines whether this attribute list element is a checked/unchecked list or not.
            $isCheck = in_array($this->getPickType($pick), [self::LIST_TYPE_CHECKED, self::LIST_TYPE_UNCHECKED], true);

            // close all nested lists which are deeper than the current list element
            while (count($openLists) > $indent + 1) {
                $output .= '</li></'.array_pop($openLists).'>'.PHP_EOL;
            }

            // close the previous list element on the same level, and when two list types
            // are after each other (OL and UL) close the previous list so the new one will open
            if (count($openLists) === $indent + 1) {
                $output .= '</li>'.PHP_EOL;
                $isItemOpen = false;
                if (end($openLists) !== $listTag) {
                    $output .= '</'.array_pop($openLists).'>'.PHP_EOL;
                    $isItemOpen = $openLists !== [];
                }
            }

            // open the OL/UL tags up to the indent level of the current list element,
            // skipped indent levels are wrapped into an empty list element to keep the html valid.
            // opening tag process has been simplified, see https://github.com/nadar/quill-delta-parser/pull/33
            // and https://github.com/nadar/quill-delta-parser/issues/30
            while (count($openLists) < $indent + 1) {
                if ($openLists !== [] && !$isItemOpen) {
                    $output .= '<li>';
                }

                $output .= '<'.$listTag;
                if ($isCheck) {
                    $output .= ' class="'.$this->checkListClass.'"';
                }

                $output .= '>'.PHP_EOL;
                $openLists[] = $listTag;
                $isItemOpen = false;
            }

            // the list element itself is closed by the next list element or when the list ends
            $output .= '<li'.$this->getAlignmentStyle($pick).'>';
            if (!$isEmpty) {
                if ($isCheck) {
                    $output .= '<input type="checkbox" disabled';
                    if ($this->getPickType($pick) === self::LIST_TYPE_CHECKED) {
                        $output .= ' checked';
                    }

                    $output .= '><label>'.$buffer.'</label>';
                } else {
                    $output .= $buffer;
                }
            }

            $isItemOpen = true;

            // close all open list elements and lists if:
            //   a. its the last element of a list serie.
            //   b. or its the last element in the picked list.
            if ($isLast || $pick->isLast()) {
                while ($openLists !== []) {
                    $output .= '</li></'.array_pop($openLists).'>'.PHP_EOL;
                }

                $isItemOpen = false;
            }

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
     * Get the style attribute for an aligned list item, or an empty string when the item is not aligned.
     *
     * @return string
     * @throws Exception for unknown alignment values
     */
    protected function getAlignmentStyle(Pick $pick)
    {
        $alignment = $pick->optionValue('alignment');
        if (!$alignment) {
            return '';
        }

        if (!in_array($alignment, $this->alignments, true)) {
            // prevent html injection in case the attribute is user input
            throw new Exception('An unknown alignment "' . $alignment . '" has been detected.');
        }

        return ' style="text-align: ' . $alignment . ';"';
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
