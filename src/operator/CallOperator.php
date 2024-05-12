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
class CallOperator extends Operator {

  /**
   * @var array<Expression>
   */
  private readonly array $args;

  private ?array $argTypes = null;

  /**
   * @param array<Expression> $args
   */
  public function __construct(array $args) {
    parent::__construct(Operator::TYPE_CALL, OperatorType::Postfix, 2, true);
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

  public function validate(Scope $scope): Type {
    $this->argTypes = [];
    /** @var Expression $arg */
    foreach($this->args as $arg) {
      $this->argTypes[] = $arg->validate($scope);
    }
  }

  public function operate(?Expression $leftExpression, ?Expression $rightExpression): Value {
    if($leftExpression === null) {
      throw new \BadFunctionCallException('Invalid operation');
    }
    return $leftExpression->run()->operate($this, null);
  }
}
