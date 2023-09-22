<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\expression\Expression;

/**
 * Superclass for all parsers
 * 
 * @author Timo Lehnertz
 *
 */
abstract class Parser {
  
  /**
   * Parses the expression
   * @param int $index The starting index. Index will be reset to its initial value if returning null
   * otherwise $index will indicate the index of the next token after this parsed part
   * @return FormulaPart|null FormulaPart in case of succsess, null in case of failure
   */
  public static function parse(array &$tokens, int &$index): ?FormulaPart {
    if(sizeof($tokens) <= $index) {
      throw new ExpressionNotFoundException('Index out of bounds');
    }
    $indexBefore = $index;
    $expression = self::parseExpression($tokens, $index);
    if($expression === null) {
      $index = $indexBefore;
    }
    return $expression;
  }
  
  /**
   * @param array $tokens array of tokens to beparsed
   * @param int $index starting index in $tokens
   * SHOULD NOT throw any exceptions
   * If parsing was succsessfull index MUST be pointing to the next token after this parsed expression
   * @return Expression|NULL Expression if parsing was sucsessfull or null if parsing was not succsessfull
   */
  protected static abstract function parsePart(array &$tokens, int &$index): ?FormulaPart;
}

