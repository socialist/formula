<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\CompoundType;

class TernaryExpressionTest extends TestCase {

  public function testTrue(): void {
    /**
     * Setup
     */
    $condition = new ConstantExpression(new BooleanType(), new BooleanValue(true), 'true');
    $leftExpression = new ConstantExpression(new FloatType(), new FloatValue(1), '1.0');
    $rightExpression = new ConstantExpression(new IntegerType(), new IntegerValue(2), '2');
    $expression = new TernaryExpression($condition, $leftExpression, $rightExpression);

    /**
     * Validate
     */
    /** @var CompoundType $type */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(CompoundType::class, $type);
    $this->assertTrue($type->equals(CompoundType::buildFromTypes([new IntegerType(),new FloatType()])));

    /**
     * Run
     */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(FloatValue::class, $result);
    $this->assertEquals(1.0, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('true?1.0:2', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('TernaryExpression', $node->nodeType);
    $this->assertCount(3, $node->connectedInputs);
    $this->assertEquals($condition->buildNode(new Scope()), $node->connectedInputs[0]);
    $this->assertEquals($leftExpression->buildNode(new Scope()), $node->connectedInputs[1]);
    $this->assertEquals($rightExpression->buildNode(new Scope()), $node->connectedInputs[2]);
    $this->assertEquals([], $node->info);
  }

  public function testFalse(): void {
    /**
     * Setup
     */
    $condition = new ConstantExpression(new BooleanType(), new BooleanValue(false), 'false');
    $leftExpression = new ConstantExpression(new FloatType(), new FloatValue(1), '1.0');
    $rightExpression = new ConstantExpression(new IntegerType(), new IntegerValue(2), '2');
    $expression = new TernaryExpression($condition, $leftExpression, $rightExpression);

    /**
     * Validate
     */
    /** @var CompoundType $type */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(CompoundType::class, $type);
    $this->assertTrue($type->equals(CompoundType::buildFromTypes([new IntegerType(),new FloatType()])));

    /**
     * Run
     */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(IntegerValue::class, $result);
    $this->assertEquals(2, $result->toPHPValue());
  }
}
