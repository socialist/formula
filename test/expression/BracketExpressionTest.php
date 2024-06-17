<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;

class BracketExpressionTest extends TestCase {

  public function testOK(): void {
    /**
     * Setup
     */
    $constantNode = (new ConstantExpression(new FloatType(), new FloatValue(123.4)))->buildNode(new Scope());
    $element = $this->createMock(ConstantExpression::class);
    $element->expects($this->once())->method('validate')->willReturn(new FloatType());
    $element->expects($this->once())->method('run')->willReturn(new FloatValue(123.4));
    $element->expects($this->once())->method('toString')->willReturn('123.4');
    $element->expects($this->once())->method('buildNode')->willReturn($constantNode);
    $expression = new BracketExpression($element);

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
    $this->assertEquals(123.4, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('(123.4)', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('BracketExpression', $node->nodeType);
    $this->assertCount(1, $node->connectedInputs);
    $this->assertEquals($constantNode, $node->connectedInputs[0]);
    $this->assertEquals([], $node->info);
  }
}
