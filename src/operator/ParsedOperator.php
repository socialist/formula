<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\expression\Expression;

/**
 * @author Timo Lehnertz
 */
abstract class ParsedOperator extends FormulaPart {

  public abstract function getPrecedence(): int;

  public abstract function getOperatorType(): OperatorType;

  /**
   * Transform this operator into an expression
   */
  public abstract function transform(?Expression $leftExpression, ?Expression $rightExpression): Expression;
}
