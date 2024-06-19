<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class ExpressionFunctionTest extends TestCase {

  public function testOK(): void {
    $formula = new Formula('array_filter({1,2,3,4}, (int a) -> a % 2 == 0)');
    $this->assertEquals([1 => 2,3 => 4], $formula->calculate()->toPHPValue());
    $this->assertEquals('array_filter({1,2,3,4},(int a) -> a%2==0)', $formula->prettyprintFormula());
  }

  public function testNodeTree(): void {
    $formula = new Formula('(boolean a) -> a');
    $rootNode = $formula->getNodeTree()->rootNode;
    $this->assertInstanceOf(IdentifierExpression::class, $rootNode->connectedInputs[0]);
  }
}
