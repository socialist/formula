<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListValue;
use TimoLehnertz\formula\PrettyPrintOptions;

class ArgumentListExpressionTest extends TestCase {

  public function testCastedExpressionException(): void {
    $expression = new ArgumentListExpression([]);
    $this->expectException(FormulaBugException::class);
    $this->expectExceptionMessage('ArgumentListExpression can only be casted to OuterFunctionArgumentListType! Got '.IntegerType::class);
    $expression->getCastedExpression(new IntegerType(), new Scope());
  }

  public function testCastedExpression(): void {
    $expression = $this->createMock(Expression::class);
    $expression->expects($this->atLeastOnce())->method('validate')->willReturn(new FloatType());
    $expressions = [$expression];
    $expression = new ArgumentListExpression($expressions);
    $argType = new OuterFunctionArgument(new IntegerType(), false);
    $type = new OuterFunctionArgumentListType([$argType], false);
    $casted = $expression->getCastedExpression($type, new Scope());
    $this->assertCount(1, $casted->getExpressions());
    $this->assertInstanceOf(OperatorExpression::class, $casted->getExpressions()[0]);
    $this->assertInstanceOf(IntegerType::class, $casted->getExpressions()[0]->validate(new Scope()));
  }

  public function testValidate(): void {
    $expression = $this->createMock(Expression::class);
    $expression->expects($this->once())->method('validate')->willReturn(new FloatType());
    $expressions = [$expression];
    $expression = new ArgumentListExpression($expressions);
    $type = $expression->validate(new Scope());
    /** @var OuterFunctionArgumentListType $type */
    $this->assertInstanceOf(OuterFunctionArgumentListType::class, $type);
    $this->assertInstanceOf(FloatType::class, $type->getArgumentType(0));
  }

  public function testRun(): void {
    $expression = $this->createMock(Expression::class);
    $expression->expects($this->once())->method('run')->willReturn(new FloatValue(123));
    $expressions = [$expression];
    $expression = new ArgumentListExpression($expressions);
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(OuterFunctionArgumentListValue::class, $result);
    $this->assertCount(1, $result->getValues());
    $this->assertEquals(123, $result->getValues()[0]->toPHPValue());
  }

  public function testToString(): void {
    $expression = new ConstantExpression(new FloatType(), new FloatValue(123), '123.0');
    $expressions = [$expression];
    $expression = new ArgumentListExpression($expressions);
    $string = $expression->toString(PrettyPrintOptions::buildDefault());
    $this->assertEquals('(123.0)', $string);
  }

  public function testBuildNode(): void {
    $constantExpression = new ConstantExpression(new FloatType(), new FloatValue(123), '123.0');
    $expressions = [$constantExpression];
    $expression = new ArgumentListExpression($expressions);
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('ArgumentListExpression', $node->nodeType);
    $this->assertCount(1, $node->connectedInputs);
    $this->assertEquals($constantExpression->buildNode(new Scope()), $node->connectedInputs[0]);
  }
}
