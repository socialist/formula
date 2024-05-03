<?php
namespace TimoLehnertz\formula\type;

/**
 * Type metadata
 * Must be immutable
 *
 * @author Timo Lehnertz
 */
interface Type {

  /**
   *
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public function getIdentifier(bool $nested = false): string;

  public function canCastTo(Type $type): bool;

  /**
   *
   * @return array<string, SubProperty>
   */
  public function getSubProperties(): array;

  /**
   *
   * @return array<int, ImplementedOperator> OperatorID => ImplementedOperator
   */
  public function getImplementedOperators(): array;
}

