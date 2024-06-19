<?php
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\functions\FormulaFunctionBody;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\InnerFunctionArgument;
use TimoLehnertz\formula\type\functions\InnerFunctionArgumentList;
use TimoLehnertz\formula\NodesNotSupportedException;

class FunctionExpressionTest extends TestCase {

  public function testOK(): void {
    /**
     * Setup
     */
    $returnType = new FloatType();
    $innerVargFunctionArgument = new InnerFunctionArgument(false, new IntegerType(), 'a', null);
    $arguments = new InnerFunctionArgumentList([$innerVargFunctionArgument], null);
    $returnStatement = new ReturnStatement(new ConstantExpression(new FloatType(), new FloatValue(123.4)));
    $codeBlock = new CodeBlock([$returnStatement], false, false);
    $expression = new FunctionExpression($returnType, $arguments, $codeBlock);

    /**
     * Validate
     */
    /** @var FunctionType $type */
    $type = $expression->validate(new Scope());
    $this->assertInstanceOf(FunctionType::class, $type);
    $this->assertEquals($returnType, $type->generalReturnType);
    $this->assertEquals($arguments->toOuterType(), $type->arguments);

    /**
     * Run
     */
    /** @var FunctionValue $result */
    $result = $expression->run(new Scope());
    $this->assertInstanceOf(FunctionValue::class, $result);
    $this->assertEquals(new FunctionValue(new FormulaFunctionBody($arguments, $codeBlock, new Scope())), $result);

    /**
     * ToString
     */
    $this->assertEquals("float (int a) {\r\n  return 123.4;\r\n}", $expression->toString(PrettyPrintOptions::buildDefault()));

    /**
     * Node
     */
    $this->expectException(NodesNotSupportedException::class);
    $this->expectExceptionMessage('FunctionExpression does not support nodes');
    $expression->buildNode(new Scope());
  }
}
