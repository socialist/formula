<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\statement\Frequency;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class FormulaFunctionBody implements FormulaPart, FunctionBody {

  private readonly InnerFunctionArgumentList $arguments;

  private readonly CodeBlock $codeBlock;

  private readonly Scope $scope;

  public function __construct(InnerFunctionArgumentList $arguments, CodeBlock $codeBlock, Scope $scope) {
    $this->arguments = $arguments;
    $this->codeBlock = $codeBlock;
    $this->scope = $scope;
  }

  public function validate(Scope $scope, Type $expectedReturnType): void {
    $scope = $this->scope->buildChild();
    $this->arguments->populateScopeDefinesOnly($scope);
    $codeBlockReturn = $this->codeBlock->validate($scope, $expectedReturnType);
    $returnTypes = [];
    if($codeBlockReturn->returnType !== null) {
      $returnTypes[] = $codeBlockReturn->returnType;
    }
    if($codeBlockReturn->returnFrequency !== Frequency::ALWAYS) {
      $returnTypes[] = new VoidType();
    }
    $implicitReturnType = CompoundType::buildFromTypes($returnTypes, false);
    if(!$expectedReturnType->assignableBy($implicitReturnType)) {
      throw new FormulaValidationException($expectedReturnType->getIdentifier().' function can\'t return '.$implicitReturnType->getIdentifier());
    }
  }

  public function call(OuterFunctionArgumentListValue $args): Value {
    $scope = $this->scope->buildChild();
    $this->arguments->populateScope($scope, $args);
    $statementReturn = $this->codeBlock->run($scope);
    if($statementReturn->returnValue === null) {
      return new VoidValue();
    } else {
      return $statementReturn->returnValue;
    }
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->arguments->tostring($prettyPrintOptions).' '.$this->codeBlock->toString($prettyPrintOptions);
  }

  public function getArgs(): OuterFunctionArgumentListType {
    return $this->arguments->toOuterType();
  }
}
