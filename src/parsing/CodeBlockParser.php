<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class CodeBlockParser extends Parser {

  private readonly bool $allowSingleLine;

  private readonly bool $root;

  public function __construct(bool $allowSingleLine, bool $root) {
    $this->allowSingleLine = $allowSingleLine;
    $this->root = $root;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($this->allowSingleLine && $firstToken->id !== Token::CURLY_BRACKETS_OPEN) {
      $parsed = (new StatementParser())->parse($firstToken);
      return new ParserReturn(new CodeBlock([$parsed->parsed], true), $parsed->nextToken);
    }
    $token = $firstToken;
    if(!$this->root) {
      if($token->id !== Token::CURLY_BRACKETS_OPEN) {
        throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
      }
      if(!$token->hasNext()) {
        throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
      }
      $token = $token->next();
    }
    $statements = [];
    while($token !== null && $token->id !== Token::CURLY_BRACKETS_CLOSED) {
      $parsed = (new StatementParser())->parse($token);
      $statements[] = $parsed->parsed;
      $token = $parsed->nextToken;
    }
    if(!$this->root) {
      if($token === null) {
        throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
      }
      if($token->id !== Token::CURLY_BRACKETS_CLOSED) {
        throw new ParsingException(ParsingException::PARSING_ERROR_EXPECTED_CLOSING_CURLY_BRACKETS, $token);
      }
      $token = $token->next();
    }
    return new ParserReturn(new CodeBlock($statements, false), $token);
  }
}
