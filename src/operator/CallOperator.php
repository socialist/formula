<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 *
 */
class CallOperator extends Operator {

  /**
   * @var array<Expression>
   */
  private readonly array $args;

  /**
   * @param array<Expression> $args
   */
  public function __construct(array $args) {
    $this->args = $args;
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

  public function getSubParts(): array {
    return $this->args;
  }

  public function validate(Scope $scope): Type {}
}

