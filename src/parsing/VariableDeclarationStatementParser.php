<?php
namespace src\parsing;

use TimoLehnertz\formula\parsing\Parser;
use TimoLehnertz\formula\statement\VariableDeclarationStatement;

class VariableDeclarationStatementParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?VariableDeclarationStatement {
    if(sizeof($token) <= $index + 2)
    if($tokens[$index]->name !== 'I') return null; // identifier
    $typeName = $tokens[$index]->value;
    $index++;
    if($tokens[$index]->name !== 'I') return null; // identifier
    $identifier = $tokens[$index]->value;
    $index++;
    if($tokens[$index]->name === ';') {
      return new VariableDeclarationStatement($scope, $typeName, $identifier, null);
    }
  }
}

