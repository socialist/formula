<?php
namespace TimoLehnertz\formula\parsing;


use TimoLehnertz\formula\expression\Expression;

/**
 * Statement ::= <> | <> | <>
 * 
 * @author Timo Lehnertz
 *
 */
class StatementParser extends Parser {

  
  
  protected static function parsePart(array &$tokens, int &$index): ?Expression {
    $parsers = [];
    $parsers []= 
  }
}

