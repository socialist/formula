<?php
namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

abstract class Expression
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Expression constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param Operator $context
     * @return float
     */
    public abstract function calculate(Operator $context): float;
}