<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
abstract class Parser {

  public readonly string $name;

  public function __construct(string $name) {
    $this->name = $name;
  }

  /**
   * @throws ParsingException
   */
  public function parse(?Token $firstToken, bool $required = false, bool $expectEnd = false): ParserReturn {
    if($firstToken === null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    try {
      $parserReturn = $this->parsePart($firstToken);
    } catch(ParsingSkippedException $e) {
      if($required) {
        throw new ParsingException($this, ParsingException::PARSING_ERROR_UNEXPECTED_TOKEN, $firstToken);
      } else {
        throw $e;
      }
    }
    if($expectEnd && $parserReturn->nextToken !== null) {
      throw new ParsingException($this, ParsingException::PARSING_ERROR_EXPECTED_EOF, $parserReturn->nextToken);
    }
    return $parserReturn;
  }

  /**
   * @return ParserReturn|ParsingException::PARSING_ERROR_*
   * @throws ParsingException
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn;
}
