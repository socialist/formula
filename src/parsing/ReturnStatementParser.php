<?php
declare(strict_types = 1);
namespace src\parsing;

use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\parsing\ParserReturn;
use TimoLehnertz\formula\parsing\ParsingException;
use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ReturnStatementParser extends Parser {

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_RETURN) {
      throw new ParsingException(ParsingException::PARSING_ERROR_GENERIC, $firstToken);
    }
    try {
      $returnExpression = (new ExpressionParser())->parse($firstToken->next());
      return new ParserReturn(new ReturnStatement($returnExpression->parsed), $returnExpression->nextToken);
    } catch(ParsingException $e) {
      return new ParserReturn(new ReturnStatement(null), $firstToken->next());
    }
  }
}
