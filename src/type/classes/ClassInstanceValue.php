<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\MemberAccsessValue;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class ClassInstanceValue extends Value {

  /**
   * @var array<string, FieldValue>
   */
  private readonly array $fields;

  public function __construct(array $fields) {
    $this->fields = $fields;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  public function copy(): Value {
    return new ClassInstanceValue($this->fields);
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        if($other instanceof MemberAccsessValue) {
          if(isset($this->fields[$other->getMemberIdentifier()])) {
            return $this->fields[$other->getMemberIdentifier()]->getValue();
          }
        }
    }
    throw new FormulaBugException('Invalid operation');
  }

  public function toPHPValue(): mixed {
    return $this;
  }

  public function toString(): string {
    return 'classInstance';
  }

  public function prettyprint(): string {}
}
