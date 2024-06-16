<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\statement\Frequency;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use TimoLehnertz\formula\FormulaValidationException;

/**
 * @author Timo Lehnertz
 */
class FormulaFunctionBody extends FormulaPart implements FunctionBody {

  private readonly InnerFunctionArgumentList $arguments;

  private readonly CodeBlock $codeBlock;

  private readonly Scope $scope;

  private ?Type $forcedReturnType;

  public function __construct(InnerFunctionArgumentList $arguments, CodeBlock $codeBlock, Scope $scope, ?Type $forcedReturnType = null) {
    parent::__construct();
    $this->arguments = $arguments;
    $this->codeBlock = $codeBlock;
    $this->scope = $scope;
    $this->forcedReturnType = $forcedReturnType;
  }

  public function validateStatement(Scope $scope, Type $expectedReturnType): void {
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
      return $statementReturn->returnValue->copy();
    }
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $str = '(';
    $del = '';
    /**  @var InnerFunctionArgument $argument */
    foreach($this->arguments as $argument) {
      $str .= $del.$argument->toString($prettyPrintOptions);
      $del = ',';
    }
    if($this->varg) {
      $str .= $del.$this->varg->toString($prettyPrintOptions);
    }
    $str .= ') '.$this->codeBlock->toString($prettyPrintOptions);
    return $str;
  }

  public function getArgs(): OuterFunctionArgumentListType {
    return $this->arguments->toOuterType();
  }
}
