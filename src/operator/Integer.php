<?php
namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Integer extends Expression
{
    /**
     * @inheritdoc
     */
    public function calculate(Operator $context): float
    {
        return (float) $this->value;
    }
}