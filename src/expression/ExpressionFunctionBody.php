<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\statement\ReturnStatement;
use const false;

/**
 * @author Timo Lehnertz
 */
class ExpressionFunctionBody extends CodeBlock {

  private readonly Expression $expression;

  public function __construct(Expression $expression) {
    parent::__construct([new ReturnStatement($expression)], false, false);
    $this->expression = $expression;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return '-> '.$this->expression->toString($prettyPrintOptions);
  }

  public function getExpression(): Expression {
    return $this->expression;
  }
}
