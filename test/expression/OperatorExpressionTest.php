<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\IntegerType;

class OperatorExpressionTest extends TestCase {

  public function testInfix(): void {
    $leftExpression = new ConstantExpression(new IntegerType(true), new IntegerValue(123));
    $rightExpression = new ConstantExpression(new IntegerType(true), new FloatValue(.123));
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);
    $value = $expression->run(new Scope());
    $this->assertInstanceOf(FloatValue::class, $value);
    $this->assertEquals(123.123, $value->toPHPValue());
  }
}
