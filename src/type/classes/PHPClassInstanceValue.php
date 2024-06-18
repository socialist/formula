<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\MemberAccsessValue;
use TimoLehnertz\formula\type\Value;
use const false;
use const true;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\PHPFunctionBody;

/**
 * @author Timo Lehnertz
 */
class PHPClassInstanceValue extends Value {

  private readonly mixed $instance;

  private readonly \ReflectionClass $reflection;

  public function __construct(mixed $instance) {
    $this->instance = $instance;
    $this->reflection = new \ReflectionClass($this->instance);
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): PHPClassInstanceValue {
    return new PHPClassInstanceValue($this->instance);
  }


  public function valueEquals(Value $other): bool {
    if($other instanceof PHPClassInstanceValue) {
      return $this->instance === $other->instance;
    }
    return false;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    switch($operator->getID()) {
      case ImplementableOperator::TYPE_MEMBER_ACCESS:
        if($other instanceof MemberAccsessValue) {
          if($this->reflection->hasProperty($other->getMemberIdentifier())) {
            return Scope::convertPHPVar($this->reflection->getProperty($other->getMemberIdentifier())->getValue($this->instance), true)[1];
          }
          if($this->reflection->hasMethod($other->getMemberIdentifier())) {
            $instance = $this->instance;
            $reflection = $this->reflection;
            return new FunctionValue(new PHPFunctionBody(function (...$args) use (&$instance, &$reflection, &$other) {
              return Scope::convertPHPVar($reflection->getMethod($other->getMemberIdentifier())->invoke($instance, ...$args), true)[1];
            }));
          }
        }
    }
    throw new FormulaBugException('Invalid operation');
  }

  public function toPHPValue(): mixed {
    return $this->instance;
  }

  public function toString(): string {
    return 'classInstance';
  }

  public function getFields(): array {
    return $this->fields;
  }
}
