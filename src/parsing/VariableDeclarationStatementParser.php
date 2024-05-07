<?php
namespace src\parsing;

use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\statement\VariableDeclarationStatement;
use TimoLehnertz\formula\parsing\TypeParser;
use TimoLehnertz\formula\parsing\ExpressionParser;

class VariableDeclarationStatementParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?VariableDeclarationStatement {
    $type = TypeParser::parseType($tokens, $index);
    if($type === null) {
      return null;
    }
    if($index >= sizeof($tokens)) {
      return null;
    }
    $token = $tokens[$index];
    if($token->name !== 'I') {
      return null; // identifier
    }
    $identifier = $tokens[$index]->value;
    $index++;
    if($index >= sizeof($tokens)) {
      return null;
    }
    $token = $tokens[$index];
    $expression = null;
    if($token->name === '=') {
      $index++;
      if($index >= sizeof($tokens)) {
        return null;
      }
      $expression = ExpressionParser::parse($tokens, $index);
      if($expression === null) {
        return null;
      }
    }
    if($index >= sizeof($tokens)) {
      return null;
    }
    $token = $tokens[$index];
    if($token->name === ';') {
      $index++;
      return new VariableDeclarationStatement($type, $identifier, $expression);
    } else {
      return null;
    }
  }
}

