<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\statement\CodeBlockOrExpression;
use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class CodeBlockOrExpressionParser extends VariantParser {

  public function __construct() {
    parent::__construct([new CodeBlockParser(false),new ExpressionParser()]);
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $parsed = parent::parsePart($firstToken);
    return new ParserReturn(new CodeBlockOrExpression($parsed->parsed), $parsed->nextToken);
  }
}
