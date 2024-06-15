<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use PHPUnit\Framework\Constraint\Operator;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertNotNull;
use TimoLehnertz\formula\type\CompoundType;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression implements Expression {

  public readonly ?Expression $leftExpression;

  public readonly ImplementableOperator $operator;

  public ?Expression $rightExpression;

  public function __construct(?Expression $leftExpression, ImplementableOperator $operator, ?Expression $rightExpression) {
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    $this->rightExpression = $rightExpression;
    switch($operator->getOperatorType()) {
      case OperatorType::PrefixOperator:
        assertNull($leftExpression, 'PrefixOperator can\'t have a left expression');
        assertNotNull($rightExpression, 'PrefixOperator requires a right expression');
        break;
      case OperatorType::InfixOperator:
        assertNotNull($leftExpression, 'InfixOperator requires a left expression');
        assertNotNull($rightExpression, 'InfixOperator requires a right expression');
        break;
      case OperatorType::PostfixOperator:
        assertNotNull($leftExpression, 'InfixOperator requires a left expression');
        assertNull($rightExpression, 'InfixOperator can\'t have a right expression');
        break;
    }
  }

  public function validate(Scope $scope): Type {
    $leftType = $this->leftExpression?->validate($scope) ?? null;
    $rightType = $this->rightExpression?->validate($scope) ?? null;
    if($this->operator->getOperatorType() === OperatorType::InfixOperator) {
      $operands = $leftType->getCompatibleOperands($this->operator);
      $this->rightExpression = OperatorExpression::castExpression($this->rightExpression, $rightType, CompoundType::buildFromTypes($operands), $scope);
      $rightType = $this->rightExpression->validate($scope);
    }
    return $this->operator->validateOperation($leftType, $rightType);
  }

  public function run(Scope $scope): Value {
    return $this->operator->operate($this->leftExpression?->run($scope) ?? null, $this->rightExpression?->run($scope) ?? null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    if($this->leftExpression !== null) {
      $string .= $this->leftExpression->toString($prettyPrintOptions);
    }
    $string .= $this->operator->toString($prettyPrintOptions);
    if($this->rightExpression !== null) {
      $string .= $this->rightExpression->toString($prettyPrintOptions);
    }
    return $string;
  }

  public function buildNode(Scope $scope): array {
    return ['type' => 'Operator','outerType' => $this->validate($scope)->buildNode(),'operator' => $this->operator->getIdentifier(),'leftNode' => $this->leftExpression?->buildNode($scope) ?? null,'rightNode' => $this->rightExpression?->buildNode($scope) ?? null];
  }

  public static function castExpression(Expression $source, Type $sourceType, Type $targetType, Scope $scope): Expression {
    if($targetType->assignableBy($sourceType)) {
      return $source;
    } else {
      if($source instanceof CastableExpression) {
        return $source->getCastedExpression($targetType, $scope);
      }
      $castableTypes = (new TypeCastOperator(false, new VoidType()))->getCompatibleOperands($sourceType);
      /** @var TypeType $castableType */
      foreach($castableTypes as $castableType) {
        $castableType = $castableType->getType();
        if($targetType->assignableBy($castableType)) {
          return new OperatorExpression($source, new ImplementableOperator(ImplementableOperator::TYPE_TYPE_CAST), new TypeExpression($castableType));
        }
      }
      throw new FormulaValidationException('No conversion from '.$sourceType->getIdentifier().' to '.$targetType->getIdentifier().' exists');
    }
  }
}
