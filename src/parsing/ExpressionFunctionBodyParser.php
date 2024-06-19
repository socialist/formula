<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\expression\ExpressionFunctionBody;

/**
 * @author Timo Lehnertz
 */
class ExpressionFunctionBodyParser extends Parser {

  public function __construct() {
    parent::__construct('Expression function body');
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    if($firstToken->id !== Token::FUNCTION_ARROW) {
      throw new ParsingSkippedException();
    }
    $parsedExpression = (new ExpressionParser())->parse($firstToken->next());
    return new ParserReturn(new ExpressionFunctionBody($parsedExpression->parsed), $parsedExpression->nextToken);
  }
}
