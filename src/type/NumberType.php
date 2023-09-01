<?php
namespace TimoLehnertz\formula\type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class NumberType extends Type {
  
  public function isAssignableWith(Type $type): bool {}

  public function getValue() {}

  public function setValue($value) {}

  protected function getTypeName(): string {}

  
}

