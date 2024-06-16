<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\NodesNotSupportedException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\functions\FormulaFunctionBody;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\InnerFunctionArgumentList;

/**
 * @author Timo Lehnertz
 */
class FunctionExpression extends Expression {

  private readonly Type $returnType;

  private readonly InnerFunctionArgumentList $arguments;

  private readonly CodeBlock $codeBlock;

  public function __construct(Type $returnType, InnerFunctionArgumentList $arguments, CodeBlock $codeBlock) {
    parent::__construct();
    $this->returnType = $returnType;
    $this->arguments = $arguments;
    $this->codeBlock = $codeBlock;
  }

  public function validateStatement(Scope $scope): Type {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    $functionBody->validate($scope, $this->returnType);
    return new FunctionType($functionBody->getArgs(), $this->returnType, true);
  }

  public function run(Scope $scope): Value {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    return new FunctionValue($functionBody);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, new Scope());
    return $functionBody->toString($prettyPrintOptions);
  }

  public function buildNode(Scope $scope): array {
    throw new NodesNotSupportedException('FunctionExpression');
  }
}
