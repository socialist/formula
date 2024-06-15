<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class VariableDeclarationStatement extends Statement {

  private Type $type;

  private readonly string $identifier;

  private Expression $initializer;

  public function __construct(Type $type, string $identifier, Expression $initializer) {
    parent::__construct();
    $this->type = $type;
    $this->identifier = $identifier;
    $this->initializer = $initializer;
  }

  public function validate(Scope $scope): StatementReturnType {
    $initializerType = $this->initializer->validate($scope);
    if(!$initializerType->equals($this->type)) {
      $this->initializer = OperatorExpression::castExpression($this->initializer, $this->initializer->validate($scope), $this->type, $scope, $this);
      $this->initializer->validate($scope);
    }
    $scope->define($this->identifier, $this->type);
    return new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
  }

  public function run(Scope $scope): StatementReturn {
    $value = $this->initializer->run($scope);
    $scope->define($this->identifier, $this->type, $value);
    return new StatementReturn(null, false, 0);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->type->getIdentifier().' '.$this->identifier.' = '.$this->initializer->toString($prettyPrintOptions).';';
  }

  public function buildNode(Scope $scope): array {}
}
