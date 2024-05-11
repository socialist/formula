<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\expression\IdentifierExpression;

/**
 * @author Timo Lehnertz
 */
class ExpressionParser extends Parser {

  private function transform(array $expressionsAndOperators, Token $nextToken): ParserReturn|int {
    if(count($expressionsAndOperators) === 0) {
      return ParsingException::PARSING_ERROR_GENERIC;
    }
  }

  // @formatter:off
  private static array $expressionEndingTokens = [
    Token::COMMA => true,
    Token::BRACKETS_CLOSED => true,
    Token::SQUARE_BRACKETS_CLOSED => true,
    Token::CURLY_BRACKETS_CLOSED => true,
    Token::COlON => true,
    Token::SEMICOLON => true,
  ];

  // @formatter:on
  protected function parsePart(Token $firstToken): ParserReturn|int {
    $token = $firstToken;
    $inBrackets = $token->id === Token::BRACKETS_OPEN;
    if($inBrackets) {
      $token = $token->next();
    }
    $expressionsAndOperators = [];
    $parsers = [new OperatorParser(),new ConstantExpressionParser()];
    while($token !== null) {
      if(isset(ExpressionParser::$expressionEndingTokens[$token->id])) {
        break;
      }
      if($token->id === Token::IDENTIFIER) {
        $expressionsAndOperators[] = new IdentifierExpression($token->value);
        $token = $token->next();
      }
      $found = false;
      foreach($parsers as $parser) {
        $parsed = $parser->parse($token);
        if(is_int($parsed)) {
          continue;
        }
        $expressionsAndOperators[] = $parsed->parsed;
        $token = $parsed->nextToken;
        break;
      }
      if(!$found) {
        return ParsingException::PARSING_ERROR_GENERIC;
      }
    }
    if($inBrackets) {
      if($token === null || $token->id !== Token::BRACKETS_CLOSED) {
        throw ParsingException::PARSING_ERROR_GENERIC;
      }
      $token = $token->next();
    }
    return $this->transform($expressionsAndOperators, $token);
  }
}
