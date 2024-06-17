<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\statement\ForEachStatement;

/**
 * @author Timo Lehnertz
 */
class ForEachStatementParser extends Parser {

  public function __construct() {
    parent::__construct('for each statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::KEYWORD_FOR) {
      throw new ParsingSkippedException();
    }
    if(!$firstToken->hasNext()) {
      throw new ParsingSkippedException();
    }
    $token = $firstToken->next();
    if($token->id !== Token::BRACKETS_OPEN) {
      throw new ParsingSkippedException();
    }
    if(!$token->hasNext()) {
      throw new ParsingSkippedException();
    }
    $token = $token->next();
    $final = $token->id === Token::KEYWORD_FINAL;
    if($final) {
      $token = $token->next();
      if($token === null) {
        throw new ParsingSkippedException();
      }
    }
    $parsedType = null;
    if($token->id === Token::KEYWORD_VAR) {
      if(!$token->hasNext()) {
        throw new ParsingSkippedException();
      }
      $token = $token->next();
    } else {
      $parsedType = (new TypeParser(false))->parse($token);
      $token = $parsedType->nextToken;
      if($token === null) {
        throw new ParsingSkippedException();
      }
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingSkippedException();
    }
    $identifier = $token->value;
    $token = $token->next();
    if($token === null) {
      throw new ParsingSkippedException();
    }
    if($token->id !== Token::COlON) {
      throw new ParsingSkippedException();
    }
    $token = $token->requireNext();
    $parsedExpression = (new ExpressionParser())->parse($token, true);
    $token = $parsedExpression->nextToken;
    if($token === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::BRACKETS_CLOSED) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_TOKEN, 'Expected )');
    }
    $token = $token->requireNext();
    $parsedCodeBlock = (new CodeBlockParser(true, false))->parse($token, true);
    $forEachStatement = new ForEachStatement($final, $parsedType?->parsed ?? null, $identifier, $parsedExpression->parsed, $parsedCodeBlock->parsed);
    return new ParserReturn($forEachStatement, $parsedCodeBlock->nextToken);
  }
}
