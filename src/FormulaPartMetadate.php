<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class FormulaPartMetadate {

  public readonly FormulaPart $formulaPart;

  public readonly Token $firstToken;

  public readonly Token $lastToken;

  public readonly string $name;

  private static array $savedMetadata = [];

  public function __construct(FormulaPart $formulaPart, Token $firstToken, Token $lastToken, string $name) {
    $this->formulaPart = $formulaPart;
    $this->firstToken = $firstToken;
    $this->lastToken = $lastToken;
    $this->name = $name;
    FormulaPartMetadate::$savedMetadata[$formulaPart->getIdentificationID()] = $this;
  }

  public static function get(FormulaPart $formulaPart): ?FormulaPartMetadate {
    if(isset(FormulaPartMetadate::$savedMetadata[$formulaPart->getIdentificationID()])) {
      return FormulaPartMetadate::$savedMetadata[$formulaPart->getIdentificationID()];
    } else {
      return null;
    }
  }

  public function getSource(?int $maxTokens = null): string {
    if($this->firstToken === $this->lastToken) {
      return $this->firstToken->source;
    }
    $source = '';
    $token = $this->firstToken;
    $tokenCount = 0;
    while($token !== $this->lastToken && ($maxTokens === null || $tokenCount < $maxTokens)) {
      $source .= $token->source;
      $token = $token->next();
      $tokenCount++;
    }
    if($token === $this->lastToken) {
      return $source;
    } else {
      return $source.'...';
    }
  }
}
