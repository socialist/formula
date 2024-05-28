<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\tokens\Token;

/**
 * CodeBlock ::= '{' ...<Statement> '}' | ...<Statement>
 *
 * @author Timo Lehnertz
 *
 */
class CodeBlockParser extends Parser {

  private readonly bool $allowSingleLine;

  public function __construct(bool $allowSingleLine) {
    $this->allowSingleLine = $allowSingleLine;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($this->allowSingleLine && $firstToken->id !== Token::CURLY_BRACKETS_OPEN) {
      $parsed = (new StatementParser())->parse($firstToken);
      return new ParserReturn(new CodeBlock([$parsed->parsed], true), $parsed->nextToken);
    }
    if($firstToken->id !== Token::CURLY_BRACKETS_OPEN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    if(!$firstToken->hasNext()) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
    }
    $token = $firstToken->next();
    $statements = [];
    while($token->id !== Token::CURLY_BRACKETS_CLOSED) {
      $parsed = (new StatementParser())->parse($token);
      $statements[] = $parsed->parsed;
      $token = $parsed->nextToken;
      if($token === null) {
        throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, null);
      }
    }
    return new ParserReturn(new CodeBlock($statements, false), $token->next());
  }
}
