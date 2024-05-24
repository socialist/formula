<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\OperatorBuilder;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;

class OperatorExpressionTest extends TestCase {

  public function testInfix(): void {
    $leftExpression = new ConstantExpression(new IntegerValue(123));
    $rightExpression = new ConstantExpression(new FloatValue(.123));
    $operator = OperatorBuilder::buildOperator(Operator::IMPLEMENTABLE_ADDITION);
    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);
    $value = $expression->run();
    $this->assertInstanceOf(FloatValue::class, $value);
    $this->assertEquals(123.123, $value->getValue());
  }

  public function testPrefix(): void {
    $leftExpression = null;
    $rightExpression = new ConstantExpression(new FloatValue(.123));
    $operator = OperatorBuilder::buildOperator(Operator::PARSABLE_INCREMENT_PREFIX);
    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);
    $value = $expression->run();
    $this->assertInstanceOf(FloatValue::class, $value);
    $this->assertEquals(1.123, $value->getValue());
  }
}
