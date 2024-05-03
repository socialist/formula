<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\procedure\CodeBlock;

/**
 * CodeBlock ::= <Statement> | '{' ...<Statement> '}'
 *
 * @author Timo Lehnertz
 *        
 */
class CodeBlockParser {

  protected static function parsePart(array &$tokens, int &$index, bool $forceBrackets = false): CodeBlock|int {
    $token = $tokens[$index];
    $insideBrackets = $token->value === '{';
    if($insideBrackets) {
      $index++;
      if($index >= sizeof($tokens)) {
        throw new UnexpectedEndOfInputException();
      }
    }
    $statements = [];
    do {
      if($index >= sizeof($tokens)) {
        throw new UnexpectedEndOfInputException();
      }
      if($insideBrackets && $tokens[$index]->value === '}') {
        $insideBrackets = false;
        $index++;
      } else {
        $statements[] = StatementParser::parse($tokens, $index);
      }
    } while($insideBrackets);
    return new CodeBlock($statements);
  }
}

