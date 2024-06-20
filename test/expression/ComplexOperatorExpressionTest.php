<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\ParsedOperator;

class ComplexOperatorExpressionTest extends TestCase {

  public function testOK(): void {
    /**
     * Setup
     */
    $innerLeftExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), '123.4');
    $innerOperator = new ImplementableOperator(ImplementableOperator::TYPE_ADDITION);
    $innerRightExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), '123.4');
    $outerLeftExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), '123.4');
    $outerOperator = $this->createMock(ParsedOperator::class);
    $outerOperator->expects($this->exactly(1))->method('toString')->willReturn('+=');
    $outerRightExpression = new ConstantExpression(new FloatType(), new FloatValue(123.4), '123.4');

    $expression = new ComplexOperatorExpression($innerLeftExpression, $innerOperator, $innerRightExpression, $outerLeftExpression, $outerOperator, $outerRightExpression);

    /**
     * Validate
     */
    /** @var FloatType $type */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(FloatType::class, $type);

    /**
     * Run
     */
    /** @var FloatValue $type */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(FloatValue::class, $result);
    $this->assertEquals(123.4 * 2, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('123.4+=123.4', $expression->toString(PrettyPrintOptions::buildDefault()));

  /**
   * Node
   */
    //     $node = $expression->buildNode(new Scope());
    //     $this->assertEquals('ComplexOperatorExpression', $node->nodeType);
    //     $this->assertCount(2, $node->connectedInputs);
    //     $this->assertEquals($outerLeftExpression->buildNode(new Scope()), $node->connectedInputs[0]);
    //     $this->assertEquals($outerRightExpression->buildNode(new Scope()), $node->connectedInputs[1]);
    //     $this->assertEquals(['operator' => '+='], $node->info);
  }
}
