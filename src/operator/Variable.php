<?php
namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Variable extends Expression
{
    protected $key;

    public function __construct(string $key, string $value = '0')
    {
        $this->key = $key;
        parent::__construct($value);
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function calculate(Operator $operator): float
    {
        $this->value = str_replace(',', '.', $this->value);
        return (float) $this->value;
    }
}