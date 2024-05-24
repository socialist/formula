<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class CallOperator extends PostfixOperator {

  /**
   * @var array<Expression>
   */
  private readonly array $args;

  private ?array $argTypes = null;

  /**
   * @param array<Expression> $args
   */
  public function __construct(array $args) {
    parent::__construct(2);
    $this->args = $args;
  }

  public function validate(Scope $scope): void {
    $this->argTypes = [];
    /** @var Expression $arg */
    foreach($this->args as $arg) {
      $this->argTypes[] = $arg->validate($scope);
    }
  }

  protected function validatePostfixOperation(Type $leftType): Type {
    return $leftType->getOperatorResultType($this, null);
  }

  protected function operatePostfix(Value $leftValue): Value {
    return $leftValue->operate($this, null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $argsStr = '';
    $delimiter = '';
    /** @var Expression $arg */
    foreach($this->args as $arg) {
      $argsStr .= $delimiter.$arg->toString($prettyPrintOptions);
      $delimiter = ',';
    }
    return '('.$argsStr.')';
  }

  public function getArgTypes(): array {
    if($this->argTypes === null) {
      throw new \BadMethodCallException('Must call validate() first!');
    }
    return $this->argTypes;
  }
}
