<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;

class IdentifierExpressionTest extends TestCase {

  public function testOK(): void {
    /**
     * Setup
     */
    $scope = new Scope();
    $scope->define(false, new FloatType(), 'abc', new FloatValue(123.4));
    $expression = new IdentifierExpression('abc');
    $this->assertEquals('abc', $expression->getIdentifier());

    /**
     * Validate
     */
    $type = $expression->validate($scope);
    $this->assertInstanceOf(FloatType::class, $type);

    /**
     * Run
     */
    $result = $expression->run($scope);
    $this->assertInstanceOf(FloatValue::class, $result);
    $this->assertEquals(123.4, $result->toPHPValue());

    /**
     * ToString
     */
    $this->assertEquals('abc', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode($scope);
    $this->assertEquals('IdentifierExpression', $node->nodeType);
    $this->assertCount(0, $node->connectedInputs);
    $this->assertEquals(['identifier' => 'abc'], $node->info);
  }
}
