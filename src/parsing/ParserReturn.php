<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use src\parsing\ParsedPart;

/**
 * @author Timo Lehnertz
 */
class ParserReturn {

  /**
   * @var ParsedPart|array<ParsedPart>
   */
  public readonly ParsedPart|array $parsed;

  public readonly ?Token $nextToken;

  /**
   * @param ParsedPart|array<ParsedPart> $parsed
   */
  public function __construct(ParsedPart|array $parsed, ?Token $nextToken) {
    $this->parsed = $parsed;
    $this->$nextToken = $nextToken;
  }
}
