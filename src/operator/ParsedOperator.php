<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 */
interface ParsedOperator {

  public function getPrecedence(): int;

  public function getOperatorType(): OperatorType;

  /**
   * Transform this operator into an expression
   */
  public function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression;

  public function toString(PrettyPrintOptions $prettyPrintOptions): string;
}
