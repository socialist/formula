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
use TimoLehnertz\formula\nodes\Node;

/**
 * @author Timo Lehnertz
 */
class FunctionExpression implements Expression {

  private ?Type $returnType;

  private readonly InnerFunctionArgumentList $arguments;

  private readonly CodeBlock $codeBlock;

  private readonly bool $implicitReturnType;

  public function __construct(?Type $returnType, InnerFunctionArgumentList $arguments, CodeBlock $codeBlock) {
    $this->returnType = $returnType;
    $this->arguments = $arguments;
    $this->codeBlock = $codeBlock;
    $this->implicitReturnType = $returnType === null;
  }

  public function validate(Scope $scope): Type {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    if($this->returnType !== null) {
      $functionBody->validate($this->returnType);
    } else {
      $this->returnType = $functionBody->validate();
    }
    return new FunctionType($functionBody->getArgs(), $this->returnType);
  }

  public function run(Scope $scope): Value {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    return new FunctionValue($functionBody);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, new Scope());
    if($this->implicitReturnType) {
      return $functionBody->toString($prettyPrintOptions);
    } else {
      return $this->returnType->getIdentifier().' '.$functionBody->toString($prettyPrintOptions);
    }
  }

  public function buildNode(Scope $scope): Node {
    if($this->codeBlock instanceof ExpressionFunctionBody) {
      return new Node('FunctionExpression', [$this->codeBlock->getExpression()], ['arguments' => 'todo']);
    } else {
      throw new NodesNotSupportedException('FunctionExpression');
    }
  }
}
