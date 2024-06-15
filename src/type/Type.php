<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;

/**
 * Must be immutable
 * @author Timo Lehnertz
 */
abstract class Type extends FormulaPart implements OperatorMeta {

  /**
   * @return string a unique identifier for this type. Equal identifier => equal type
   */
  public abstract function getIdentifier(bool $nested = false): string;

  public abstract function equals(Type $type): bool;

  public abstract function assignableBy(Type $type): bool;

  public abstract function buildNode(): array;

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return $this->getIdentifier();
  }
}
