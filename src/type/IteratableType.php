<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

/**
 * @author Timo Lehnertz
 */
interface IteratableType {

  public function getElementsType(): Type;
}
