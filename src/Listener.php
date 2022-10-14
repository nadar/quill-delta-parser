<?php

namespace nadar\quill;

/**
 * Listener Object.
 *
 * Every type of element is a listener. Listeners are "listening" to every line of delta code and can
 * pick and process this line.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
abstract class Listener
{
    /**
     * @var integer Type inline listener
     */
    public const TYPE_INLINE = 1;

    /**
     * @var integer Type block listener
     */
    public const TYPE_BLOCK = 2;

    /**
     * @var integer First priority listener within the given type
     */
    public const PRIORITY_EARLY_BIRD = 1;

    /**
     * @var integer Second priority listener within the given type. This is currently only used
     * for TEXT listeners - as they need to be the very last entry.
     */
    public const PRIORITY_GARBAGE_COLLECTOR = 2;

    /**
     * Get the listener type, either INLINE (1) or BLOCK (2)
     */
    abstract public function type(): int;

    /**
     * Process the line
     *
     * @return void
     */
    abstract public function process(Line $line);

    /**
     * Get the priority either 1 or 2
     */
    public function priority(): int
    {
        return self::PRIORITY_EARLY_BIRD;
    }

    /**
     * @var array<mixed>
     */
    private $_picks = [];

    /**
     * Pick a certain line during the process() process in order to use them later in render method.
     *
     * If a line is picked, the status of the line switches to picked.
     *
     * @param Line $line
     * @param array<mixed> $options
     * @return void
     */
    public function pick(Line $line, array $options = [])
    {
        $line->setPicked();
        $line->debugInfo('picked by ' . static::class);
        $this->_picks[] = new Pick($this, $line, $options, count($this->_picks));
    }

    /**
     * Returns an array with all picked lineds.
     *
     * @return Pick[] An array with Line objects, for IDE purposes we return the Line object as phpdoc
     */
    public function picks(): array
    {
        return $this->_picks;
    }

    /**
     * The render method is processed after the process() method is done.
     *
     * Its the right place to go forward and backward in lines if you need to, as at the point all lines
     * are allready processed trough process() method.
     *
     * @param Lexer $lexer
     * @return void
     */
    public function render(Lexer $lexer)
    {
    }
}
