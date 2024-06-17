<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\statement\CodeBlockOrExpression;

/**
 * @author Timo Lehnertz
 */
class CodeBlockOrExpressionParser extends Parser {

  public function __construct() {
    parent::__construct('codeblock or expression');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    try {
      $parsedExpression = (new ExpressionParser())->parse($firstToken);
      if($parsedExpression->nextToken !== null) {
        throw new ParsingSkippedException();
      }
      return new ParserReturn(new CodeBlockOrExpression($parsedExpression->parsed), $parsedExpression->nextToken);
    } catch(ParsingSkippedException $e) {
      $parsed = (new CodeBlockParser(false, true))->parse($firstToken);
      return new ParserReturn(new CodeBlockOrExpression($parsed->parsed), $parsed->nextToken);
    }
  }
}
