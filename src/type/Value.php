<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
interface Value {

  public function getType(): Type;

  public function isTruthy(): bool;

  public function copy(): Value;

  public function operate(OperatableOperator $operator, ?Value $other): Value;

  public function getOperatorResultType(OperatableOperator $operator, ?Type $otherType): ?Type;

  public function toString(PrettyPrintOptions $prettyPrintOptions): string;

  public function valueEquals(Value $other): bool;
}
