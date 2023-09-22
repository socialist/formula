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
class AndOperator extends Operator {

  public function __construct() {
    parent::__construct('&&', 14, true, true);
  }
  
  /**
   * @see \TimoLehnertz\formula\operator\Operator::doCalculate()
   */
  public function doCalculate(Calculateable $left, Calculateable $right): Calculateable {
    return new BooleanExpression($left->calculate()->isTruthy() && $right->calculate()->isTruthy());
  }

  public function validate(Scope $scope, ?Expression $leftExpression, ?Expression $rightExpression, FormulaSettings $formulaSettings): Type {
    $leftType = $leftExpression->validate($formulaSettings);
    $rightType = $rightExpression->validate($formulaSettings);
//     if()
  }
}

