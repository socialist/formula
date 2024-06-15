<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPartMetadate;
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
   * @throws ParsingSkippedException
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
    // Attatch metadata (optional, will improve exceptions)
    $this->attatchMetadata($firstToken, $parserReturn);
    return $parserReturn;
  }

  private function attatchMetadata(Token $firstToken, ParserReturn $parserReturn): void {
    if(is_array($parserReturn->parsed)) {
      return;
    }
    if($parserReturn->nextToken !== null) {
      $lastToken = $parserReturn->nextToken->prev();
    } else {
      $lastToken = $firstToken->last();
    }
    new FormulaPartMetadate($parserReturn->parsed, $firstToken, $lastToken, $this->name);
  }

  /**
   * @throws ParsingException
   * @throws ParsingSkippedException
   */
  protected abstract function parsePart(Token $firstToken): ParserReturn;
}
