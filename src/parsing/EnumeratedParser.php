<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\tokens\Token;

class EnumeratedParser extends Parser {

  private readonly Parser $elementParser;

  private readonly int $firstToken;

  private readonly int $delimiterToken;

  private readonly int $lastToken;

  private readonly bool $allowEmpty;

  private readonly bool $allowLastDelimiter;

  public function __construct(Parser $elementParser, int $firstToken, int $delimiterToken, int $lastToken, bool $allowEmpty, bool $allowLastDelimiter) {
    $this->elementParser = $elementParser;
    $this->firstToken = $firstToken;
    $this->delimiterToken = $delimiterToken;
    $this->lastToken = $lastToken;
    $this->allowEmpty = $allowEmpty;
    $this->allowLastDelimiter = $allowLastDelimiter;
  }

  protected function parsePart(Token $firstToken): ParserReturn|int {
    if($firstToken->id !== $this->firstToken) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
    $token = $firstToken->requireNext();
    $allowedDelimiters = $this->allowEmpty ? PHP_INT_MAX : 0;
    $requireDelimiter = false;
    $lastWasDelimiter = false;
    $parsed = [];
    while($token !== null) {
      if($token->id === $this->lastToken) {
        if($lastWasDelimiter && !$this->allowLastDelimiter) {
          return ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS;
        }
        return new ParserReturn($parsed, $token->next());
      }
      if($token->id === $this->delimiterToken) {
        if($allowedDelimiters > 0) {
          $allowedDelimiters--;
          $requireDelimiter = false;
          $lastWasDelimiter = true;
          continue;
        } else {
          return ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS;
        }
      }
      if($requireDelimiter) {
        return ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS;
      }

      $element = $this->elementParser->parse($token);
      if(is_int($element)) {
        return $element;
      }
      $parsed[] = $element;
      $requireDelimiter = true;
      $allowedDelimiters = $this->allowEmpty ? PHP_INT_MAX : 1;
      $lastWasDelimiter = false;
      $token = $token->next();
    }
    return ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT;
  }
}

