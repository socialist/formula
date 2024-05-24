<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 *
 *         Superclass for all parsers
 *
 */
abstract class Parser {

  //   public function parseRequired(?Token $firstToken): ParserReturn {
  //     $parsed = $this->parse($firstToken);
  //     if(is_int($parsed)) {
  //       throw new ParsingException($parsed, $firstToken);
  //     }
  //     return $parsed;
  //   }
  public function parse(?Token $firstToken): ParserReturn|int {
    if($firstToken === null) {
      return ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT;
    }
    return $this->parsePart($firstToken);
  }

  /**
   * @return ParserReturn|ParsingException::PARSING_ERROR_*
   * @throws UnexpectedEndOfInputException
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn|int;
}

