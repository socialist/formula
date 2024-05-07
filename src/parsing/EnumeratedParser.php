<?php
namespace src\parsing;

use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\FormulaPart;

class EnumeratedParser extends Parser {

  private readonly Parser $elementParser;

  private readonly int $delimiterToken;

  private readonly int $endToken;
  
  public function __construct(Parser $elementParser, int $delimiterToken, int $endToken) {
    parent::__construct();
    $this->elementParser = $elementParser;
    $this->delimiterToken = $delimiterToken;
    $this->endToken = $endToken;
  }
  
  protected function parsePart(Token &$token): ?array {
    $first = true;
    $parsed = [];
    while(true) {
      $element = $this->elementParser->parse($token);
      if($element === null) {
        return null;
      }
      $parsed[] = $element;
      if($token === null) {
        return null;
      }
      if($token->id === $this->endToken) {
        break;
      }
      if($token->id !== $this->delimiterToken) {
        return null;
      }

    }
    return $parsed;
  }
}

