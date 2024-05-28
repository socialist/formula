<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
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

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== $this->firstToken) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    $token = $firstToken->next();
    $allowedDelimiters = $this->allowEmpty ? PHP_INT_MAX : 0;
    $requireDelimiter = false;
    $lastWasDelimiter = false;
    $parsed = [];
    while($token !== null) {
      if($token->id === $this->lastToken) {
        if($lastWasDelimiter && !$this->allowLastDelimiter) {
          throw new ParsingException(ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $token);
        }
        return new ParserReturn($parsed, $token->next());
      }
      if($token->id === $this->delimiterToken) {
        if($allowedDelimiters > 0) {
          $allowedDelimiters--;
          $requireDelimiter = false;
          $lastWasDelimiter = true;
          $token = $token->next();
          continue;
        } else {
          throw new ParsingException(ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $token);
        }
      }
      if($requireDelimiter) {
        throw new ParsingException(ParsingException::PARSING_ERROR_MISSING_DELIMITERS, $token);
      }

      $result = $this->elementParser->parse($token);
      $parsed[] = $result->parsed;
      $requireDelimiter = true;
      $allowedDelimiters = $this->allowEmpty ? PHP_INT_MAX : 1;
      $lastWasDelimiter = false;
      $token = $result->nextToken;
    }
    throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $token);
  }
}
