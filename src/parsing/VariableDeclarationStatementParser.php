<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\VariableDeclarationStatement;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class VariableDeclarationStatementParser extends Parser {

  public function __construct() {
    parent::__construct('variable declaration statement');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $token = $firstToken;
    $final = $token->id === Token::KEYWORD_FINAL;
    if($final) {
      $token = $token->requireNext();
    }
    $isAutoType = $token->id === Token::KEYWORD_VAR;
    $parsedType = null;
    if($isAutoType) {
      $token = $token->next();
    } else {
      $parsedType = (new TypeParser(false))->parse($token);
      $token = $parsedType->nextToken;
    }
    if($token === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    if($token->id !== Token::IDENTIFIER) {
      throw new ParsingSkippedException();
    }
    $identifier = $token->value;
    $token = $token->requireNext();
    if($token->id !== Token::ASSIGNMENT) {
      throw new ParsingSkippedException();
    }
    $parsedInitializer = (new ExpressionParser())->parse($token->next(), true);
    if($parsedInitializer->nextToken === null) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_END_OF_INPUT);
    }
    $token = $parsedInitializer->nextToken;
    if($token->id !== Token::SEMICOLON) {
      throw new ParsingException(ParsingException::ERROR_UNEXPECTED_TOKEN, $token, 'Expected ;');
    }
    return new ParserReturn(new VariableDeclarationStatement($final, $parsedType?->parsed ?? null, $identifier, $parsedInitializer->parsed), $token->next());
  }
}
