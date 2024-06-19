<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\functions\FormulaFunctionBody;
use TimoLehnertz\formula\type\functions\InnerFunctionArgumentList;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class FunctionStatement extends Statement {

  private readonly Type $returnType;

  private readonly string $identifier;

  private readonly InnerFunctionArgumentList $arguments;

  private readonly CodeBlock $codeBlock;

  private FunctionType $functionType;

  public function __construct(Type $returnType, string $identifier, InnerFunctionArgumentList $arguments, CodeBlock $codeBlock) {
    parent::__construct();
    $this->returnType = $returnType;
    $this->identifier = $identifier;
    $this->arguments = $arguments;
    $this->codeBlock = $codeBlock;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    $functionBody->validate($this->returnType);
    $this->functionType = new FunctionType($this->arguments->toOuterType(), $this->returnType, true);
    $scope->define(true, $this->functionType, $this->identifier);
    return new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
  }

  public function runStatement(Scope $scope): StatementReturn {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, $scope);
    $functionValue = new FunctionValue($functionBody);
    $scope->define(true, $this->functionType, $this->identifier, $functionValue);
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    $functionBody = new FormulaFunctionBody($this->arguments, $this->codeBlock, new Scope());
    return $this->functionType->returnType->getIdentifier().' '.$this->identifier.$functionBody->toString($prettyPrintOptions);
  }
}
