<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\CodeBlock;

/**
 * CodeBlock ::= <Statement> | '{' ...<Statement> '}'
 * 
 * @author Timo Lehnertz
 *
 */
class CodeBlockParser extends Parser {

  protected static function parsePart(array &$tokens, int &$index): ?FormulaPart {
    if($tokens[$index]->value === '{') {
      $index++; // skipping {
      if(sizeof($tokens) <= $index) return null;
      $statements = [];
      for ($index; $index < sizeof($tokens); $index++) {
        if($tokens[$index]->value === '}') {
          $index++;
          return new CodeBlock($statements);
        } else if($statement = StatementParser::parse($tokens, $index) !== null) {
          $statements[] = $statement;
        } else {
          return null;
        }
      }
    } else {
      return StatementParser::parse($tokens, $index);
    }
  }
}

