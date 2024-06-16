<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\IteratableType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\IteratableValue;
use TimoLehnertz\formula\FormulaBugException;

/**
 * @author Timo Lehnertz
 */
class ForEachStatement extends Statement {

  private readonly bool $final;

  private ?Type $elementType;

  private readonly string $elementIdentifier;

  private Expression $getterExpression;

  private CodeBlock $body;

  public function __construct(bool $final, ?Type $elementType, string $elementIdentifier, Expression $getterExpression, CodeBlock $body) {
    parent::__construct();
    $this->final = $final;
    $this->elementType = $elementType;
    $this->elementIdentifier = $elementIdentifier;
    $this->getterExpression = $getterExpression;
    $this->body = $body;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $getterType = $this->getterExpression->validate($scope);
    if(!($getterType instanceof IteratableType)) {
      throw new FormulaValidationException('For each getter must iteratable');
    }
    if($this->elementType === null) {
      $this->elementType = $getterType->getElementsType();
    } else if(!$this->elementType->assignableBy($getterType->getElementsType(), false)) {
      throw new FormulaValidationException($this->elementType->getIdentifier().' is not assignable by '.$getterType->getElementsType()->getIdentifier());
    }
    $this->elementType = $this->elementType->setFinal($this->final);
    $scope = $scope->buildChild();
    $scope->define($this->final, $this->elementType, $this->elementIdentifier);
    $statementReturn = new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
    return $statementReturn->concatOr($this->body->validate($scope, $allowedReturnType));
  }

  public function runStatement(Scope $scope): StatementReturn {
    $iterator = $this->getterExpression->run($scope);
    if(!($iterator instanceof IteratableValue)) {
      throw new FormulaBugException('Expected iterator');
    }
    foreach($iterator->getIterator() as $value) {
      $loopScope = $scope->buildChild();
      $loopScope->define($this->final, $this->elementType, $this->elementIdentifier, $value);
      $return = $this->body->run($loopScope);
      if($return->returnValue !== null) {
        return $return;
      }
      if($return->breakFlag) {
        break;
      }
    }
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    $typeStr = $this->elementType === null ? 'var' : $this->elementType->getIdentifier();
    return 'for ('.$typeStr.' '.$this->elementIdentifier.' : '.$this->getterExpression->toString($prettyPrintOptions).') '.$this->body->toString($prettyPrintOptions);
  }
}
