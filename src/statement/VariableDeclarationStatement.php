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

  private readonly bool $final;

  private ?Type $type;

  private readonly string $identifier;

  private Expression $initializer;

  public function __construct(bool $final, ?Type $type, string $identifier, Expression $initializer) {
    parent::__construct();
    $this->final = $final;
    $this->type = $type;
    $this->identifier = $identifier;
    $this->initializer = $initializer;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    $implicitType = $this->initializer->validate($scope);
    if($this->type !== null) {
      $this->initializer = OperatorExpression::castExpression($this->initializer, $implicitType, $this->type, $scope, $this);
      $this->initializer->validate($scope);
    } else {
      $this->type = $implicitType;
    }
    $scope->define($this->final, $this->type, $this->identifier);
    return new StatementReturnType(null, Frequency::NEVER, Frequency::NEVER);
  }

  public function runStatement(Scope $scope): StatementReturn {
    $value = $this->initializer->run($scope);
    $scope->define($this->final, $this->type, $this->identifier, $value);
    return new StatementReturn(null, false, false);
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->type->getIdentifier().' '.$this->identifier.' = '.$this->initializer->toString($prettyPrintOptions).';';
  }

  public function buildNode(Scope $scope): array {}
}
