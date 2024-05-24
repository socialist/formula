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
   * @var FormulaPart|array<ParsedPart>|Type
   */
  public readonly FormulaPart|array|Type $parsed;

  public readonly ?Token $nextToken;

  /**
   * @param FormulaPart|array<ParsedPart>|Type $parsed
   */
  public function __construct(FormulaPart|array|Type $parsed, ?Token $nextToken) {
    $this->parsed = $parsed;
    $this->nextToken = $nextToken;
  }
}