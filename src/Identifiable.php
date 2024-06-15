<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
interface Identifiable {

  public function getIdentificationID(): int;
}
