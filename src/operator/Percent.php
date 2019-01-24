<?php
namespace socialist\formula\operator;

use socialist\formula\expression\Operator;

class Percent extends Expression
{
    /**
     * Percent constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $value = str_replace('%', '', $value);
        parent::__construct($value);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function calculate(Operator $context): float
    {
        if ($context->getLeftOperator() instanceof Percent) {
            return round(($this->value / 100), 2);
        } else if ($context->getRightOperator() instanceof Percent) {
            return round((($this->value / 100) * $context->getLeftOperator()->calculate($context)), 2);
        }

        throw new \Exception("This object must be the left or the right operand of OperatorExpression object");
    }
}