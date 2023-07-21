<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\procedure\CodeBlock;
use src\UnexpectedEndOfInputException;
use TimoLehnertz\formula\ExpressionNotFoundException;

class CodeBlockParser {

  public static function parse(array &$tokens, int &$index): CodeBlock {
    if(sizeof($tokens) <= $index) throw new UnexpectedEndOfInputException();
    $executables = [];
    for ($index; $index < sizeof($tokens); $index++) {
      if($formulaExpression = FormulaExpressionParser::parse($tokens, $index, false) !== null) {
        $executables[] = $formulaExpression;
      } else if($codeBlock = self::parse($tokens, $index) !== null) {
        $executables[] = $codeBlock;
      }else {
        throw new ExpressionNotFoundException('Invalid formula', $tokens, $index);
      }
    }
    return new CodeBlock($executables);
  }
}

