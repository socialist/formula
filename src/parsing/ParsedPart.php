<?php
namespace src\parsing;

use TimoLehnertz\formula\FormulaPart;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ParsedPart {

  public readonly FormulaPart $parsedPart;

  public readonly int $nextIndex;

  public function __construct(FormulaPart $parsedPart, int $nextIndex) {
    $this->parsedPart = $parsedPart;
    $this->nextIndex = $nextIndex;
  }
}
