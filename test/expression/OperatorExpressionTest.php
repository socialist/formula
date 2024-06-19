<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;

class OperatorExpressionTest extends TestCase {

  public function testInfix(): void {
    /**
     * Setup
     */
    $leftExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), "123.4");
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $rightExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), "123.4");

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    /**
     * Validate
     */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(FloatType::class, $type);

    /**
     * Run
     */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(FloatValue::class, $result);
    $this->assertEquals(123.4 * 2, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('123.4+123.4', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('OperatorExpression', $node->nodeType);
    $this->assertCount(2, $node->connectedInputs);
    $this->assertEquals($leftExpression->buildNode(new Scope()), $node->connectedInputs[0]);
    $this->assertEquals($rightExpression->buildNode(new Scope()), $node->connectedInputs[1]);
    $this->assertEquals(['operator' => $operator->getID()], $node->info);
  }

  public function testPrefix(): void {
    /**
     * Setup
     */
    $leftExpression = null;
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_UNARY_MINUS);
    $rightExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), "123.4");

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    /**
     * Validate
     */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(FloatType::class, $type);

    /**
     * Run
     */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(FloatValue::class, $result);
    $this->assertEquals(-123.4, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('-123.4', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('OperatorExpression', $node->nodeType);
    $this->assertCount(1, $node->connectedInputs);
    $this->assertEquals($rightExpression->buildNode(new Scope()), $node->connectedInputs[0]);
    $this->assertEquals(['operator' => $operator->getID()], $node->info);
  }

  public function testPrefixNoImplementedOperator(): void {
    $leftExpression = null;
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_UNARY_MINUS);
    $rightExpression = new ConstantExpression(new StringType(), new StringValue('123.4'), "'123.4'");

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('Invalid operation  - String');
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(FloatType::class, $type);
  }

  public function testInfixNoImplementedOperator(): void {
    $leftExpression = new ConstantExpression(new StringType(), new StringValue('123.4'), "'123.4'");
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION);
    $rightExpression = new ConstantExpression(new StringType(), new StringValue('123.4'), "'123.4'");

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('String does not implement operator ::');
    $expression->validate(new Scope());
  }

  public function testRunInvalidOperatorType(): void {
    $leftExpression = new ConstantExpression(new StringType(), new StringValue('123.4'), "'123.4'");
    $operator = $this->createMock(ImplementableOperator::class);
    $operator->expects($this->atLeastOnce())->method('getOperatorType')->willReturn(OperatorType::PostfixOperator);
    $rightExpression = new ConstantExpression(new StringType(), new StringValue('123.4'), "'123.4'");

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $this->expectException(FormulaBugException::class);
    $this->expectExceptionMessage('Invalid operatorType');
    $expression->run(new Scope());
  }

  public function testImplicitCastCastable(): void {
    $scope = new Scope();
    $scope->define(false, new FunctionType(new OuterFunctionArgumentListType([], false), new VoidType()), 'abc');
    $leftExpression = new IdentifierExpression('abc');
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_CALL);
    $rightExpression = $this->createMock(ArgumentListExpression::class);
    $castedExpression = new ArgumentListExpression([]);
    $rightExpression->expects($this->once())->method('getCastedExpression')->willReturn($castedExpression);

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $this->assertNotEquals($castedExpression, $expression->rightExpression);
    $expression->validate($scope);
    $this->assertEquals($castedExpression, $expression->rightExpression);
  }

  public function testImplicitCast(): void {
    $leftExpression = new ConstantExpression(new StringType(), new StringValue('12'), '"12"');
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $rightExpression = new ConstantExpression(new FloatType(), new FloatValue(3.4), '3.4');

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $returnType = $expression->validate(new Scope());
    $this->assertInstanceOf(OperatorExpression::class, $expression->rightExpression);
    $this->assertEquals($rightExpression, $expression->rightExpression->leftExpression);
    $this->assertEquals(ImplementableOperator::TYPE_TYPE_CAST, $expression->rightExpression->operator->getID());
    $this->assertInstanceof(TypeExpression::class, $expression->rightExpression->rightExpression);
    $this->assertInstanceOf(StringType::class, $returnType);

    $returnValue = $expression->run(new Scope());
    $this->assertEquals('123.4', $returnValue->toPHPValue());
  }

  public function testInvalidImplicitCast(): void {
    $leftExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), '123.4');
    $operator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $rightExpression = new ConstantExpression(new NullType(), new NullValue(), 'null');

    $expression = new OperatorExpression($leftExpression, $operator, $rightExpression);

    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('Unable to convert null to int|float');
    $expression->validate(new Scope());
  }
}
