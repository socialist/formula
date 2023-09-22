<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaSettings;
use TimoLehnertz\formula\expression\BooleanExpression;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class NotEqualsOperator extends Operator {

  public function __construct() {
    parent::__construct('!=', 10, false, true);
  }
  
  /**
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->getValue() != $right->calculate()->getValue());
  }

  public function validate(Scope $scope, ?Expression $leftExpression, ?Expression $rightExpression, FormulaSettings $formulaSettings): Type {
    /**
     * @todo
     */
  }
}

