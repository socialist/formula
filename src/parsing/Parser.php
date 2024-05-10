<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\tokens\Token;

/**
 * Superclass for all parsers
 *
 * @author Timo Lehnertz
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
  public function parse(?Token $firstToken): FormulaPart|int {
    if($firstToken === null) {
      ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT;
    }
    try {
      return $this->parsePart($firstToken);
    } catch(UnexpectedEndOfInputException $e) {
      return ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT;
    }
  }

  /**
   *
   * @return ParserReturn|ParsingException::PARSING_ERROR_*
   * @throws UnexpectedEndOfInputException
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn|int;
}

