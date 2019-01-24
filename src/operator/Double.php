<?php
namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Double extends Expression
{
    /**
     * @inheritdoc
     */
    public function calculate(Operator $context): float
    {
        $this->value = str_replace(',', '.', $this->value);
        return (float) $this->value;
    }
}