<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 */
class ParserReturn {

  /**
   * @var FormulaPart|array<FormulaPart>
   */
  public readonly FormulaPart|array $parsed;

  public readonly ?Token $nextToken;

  /**
   * @param FormulaPart|array<FormulaPart>|Type $parsed
   */
  public function __construct(FormulaPart|array $parsed, ?Token $nextToken) {
    $this->parsed = $parsed;
    $this->nextToken = $nextToken;
  }
}
