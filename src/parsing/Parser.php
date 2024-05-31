<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
abstract class Parser {

  /**
   * @throws ParsingException
   */
  public function parse(?Token $firstToken, bool $expectEnd = false): ParserReturn {
    if($firstToken === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $parserReturn = $this->parsePart($firstToken);
    if($expectEnd && $parserReturn->nextToken !== null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_EOF, $parserReturn->nextToken);
    }
    return $parserReturn;
  }

  /**
   * @return ParserReturn|ParsingException::PARSING_ERROR_*
   * @throws ParsingException
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn;
}
