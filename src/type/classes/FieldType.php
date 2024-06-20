<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class FieldType {

  public readonly bool $final;

  public readonly Type $type;

  public function __construct(bool $final, Type $type) {
    $this->final = $final;
    $this->type = $type->setFinal($this->final);
  }

  public function equals(FieldType $other): bool {
    return $this->final === $other->final && $this->type->equals($other->type);
  }
}
