<?php

namespace nadar\quill;

/**
 * Listener Object.
 *
 * Every type of element is a listenere. Listeneres are "listening" to every line of delta code and can
 * pick and process this line.
 *
 * @author Basil Suter <basil@nadar.io>
 */
abstract class Listener
{
    /**
     * @var integer Type inline listener
     */
    const TYPE_INLINE = 1;

    /**
     * @var integer Type block listener
     */
    const TYPE_BLOCK = 2;
    
    /**
     * @var integer First priority listener within the given type
     */
    const PRIORITY_EARLY_BIRD = 1;

    /**
     * @var integer Second priority listener within the given type. This is currently only used
     * for TEXT listeneres - as they need to be the very last entry.
     */
    const PRIORITY_GARBAGE_COLLECTOR = 2;

    /**
     * Undocumented function
     *
     * @return integer
     */
    abstract public function type(): int;

    /**
     * Undocumented function
     *
     * @param Line $line
     * @return void
     */
    abstract public function process(Line $line);

    /**
     * Undocumented function
     *
     * @return integer
     */
    public function priority(): int
    {
        return self::PRIORITY_EARLY_BIRD;
    }

    private $_picks = [];

    /**
     * Undocumented function
     *
     * @param Line $line
     * @param array $options
     * @return void
     */
    public function pick(Line $line, array $options = [])
    {
        $line->setPicked();
        $this->_picks[] = new Pick($line, $options, count($this->_picks), $this);
    }

    /**
     * Undocumented function
     *
     * @return Line An array with Line objects, for IDE purposes we return the Line object as phpdoc
     */
    public function picks() : array
    {
        return $this->_picks;
    }

    /**
     * Undocumented function
     *
     * @param Lexer $lexer
     * @return void
     */
    public function render(Lexer $lexer)
    {
        // override in your listenere if needed.
    }
}
