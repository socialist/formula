<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class MemberAccsessValue extends Value {

  private string $memberIdentifier;

  /**
   * @param array<array-key, Value>
   */
  public function __construct(string $memberIdentifier) {
    $this->memberIdentifier = $memberIdentifier;
  }

  public function getMemberIdentifier(): string {
    return $this->memberIdentifier;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): Value {
    return new MemberAccsessValue($this->memberIdentifier);
  }

  public function valueEquals(Value $other): bool {
    return ($other instanceof MemberAccsessValue) && $other->memberIdentifier === $this->memberIdentifier;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    throw new FormulaBugException('Invalid operator!');
  }

  public function toPHPValue(): mixed {
    return $this->memberIdentifier;
  }

  public function toString(): string {
    return $this->memberIdentifier;
  }
}
