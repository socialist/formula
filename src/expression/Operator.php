<?php
namespace socialist\formula\expression;

use socialist\formula\operator\Expression;

abstract class Operator extends Expression
{
    /**
     * @var Expression
     */
    protected $leftOperator;

    /**
     * @var Expression
     */
    protected $rightOperator;

    /**
     * Operator constructor.
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct(Expression $left, Expression $right)
    {
        parent::__construct('');
        $this->leftOperator = $left;
        $this->rightOperator = $right;
    }

    /**
     * @inheritdoc
     */
    public function calculate(Operator $context): float
    {
        $left = $this->leftOperator->calculate($this);
        $right = $this->rightOperator->calculate($this);

        return (float) $this->doCalculate($left, $right);
    }

    /**
     * @return Expression
     */
    public function getLeftOperator(): Expression
    {
        return $this->leftOperator;
    }

    /**
     * @return Expression
     */
    public function getRightOperator(): Expression
    {
        return $this->rightOperator;
    }

    /**
     * @param float $left
     * @param float $right
     * @return float
     */
    protected abstract function doCalculate(float $left, float $right): float;
}