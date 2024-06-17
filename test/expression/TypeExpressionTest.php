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
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\TypeValue;

class TypeExpressionTest extends TestCase {

  public function testOK(): void {
    /**
     * Setup
     */
    $type = new IntegerType();
    $expression = new TypeExpression($type);

    /**
     * Validate
     */
    /** @var TypeType $returnType */
    $returnType = $expression->validate(new Scope());
    $this->assertInstanceOf(TypeType::class, $returnType);
    $this->assertEquals($type, $returnType->getType());

    /**
     * Run
     */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(TypeValue::class, $result);
    $this->assertEquals($type, $result->getValue());

    /**
     * ToString
     */
    $this->assertEquals('int', $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $node = $expression->buildNode(new Scope());
    $this->assertEquals('TypeExpression', $node->nodeType);
    $this->assertCount(0, $node->connectedInputs);
    $this->assertEquals(['type' => $type->buildNodeInterfaceType()], $node->info);
  }
}
