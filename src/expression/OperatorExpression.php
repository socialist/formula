<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\TypeType;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression implements Expression {

  public readonly ?Expression $leftExpression;

  public readonly Operator $operator;

  public ?Expression $rightExpression;

  public function __construct(?Expression $leftExpression, Operator $operator, ?Expression $rightExpression) {
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    $this->rightExpression = $rightExpression;
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
          return new OperatorExpression($source, new TypeCastOperator(false, $castableType), new TypeExpression($castableType));
        }
      }
      throw new FormulaValidationException('No conversion from '.$sourceType->getIdentifier().' to '.$targetType->getIdentifier().' exists');
    }
  }

  /**
   * @param Type[] $targetTypes
   */
  public static function castExpressionToOnOf(Expression $source, Type $sourceType, array $targetTypes, Scope $scope): Expression {
    $error = null;
    /** @var Type $targetType */
    foreach($targetTypes as $targetType) {
      try {
        return OperatorExpression::castExpression($source, $sourceType, $targetType, $scope);
      } catch(FormulaValidationException $e) {
        $error = $e;
      }
    }
    if($error !== null) {
      throw $error;
    }
    if(isset($targetTypes[0])) {
      throw new FormulaValidationException('No conversion from '.$sourceType->getIdentifier().' to '.$targetTypes[0]->getIdentifier().' exists');
    } else {
      throw new FormulaValidationException('No conversion from '.$sourceType->getIdentifier().' to <empty> exists');
    }
  }

  public function validate(Scope $scope): Type {
    $leftType = $this->leftExpression?->validate($scope) ?? null;
    $rightType = $this->rightExpression?->validate($scope) ?? null;
    if($leftType !== null && $rightType !== null) {
      $operands = $this->operator->getCompatibleOperands($leftType);
      //       if(count($operands) === 0) {
      //         var_dump($leftType);
      //         var_dump($this->operator);
      //       }
      $found = false;
      /** @var Type $operand */
      foreach($operands as $operand) {
        if($operand->assignableBy($rightType)) {
          $found = true;
          break;
        }
      }
      if(!$found) { // insert type cast
        $this->rightExpression = OperatorExpression::castExpressionToOnOf($this->rightExpression, $rightType, $operands, $scope);
        $rightType = $this->rightExpression->validate($scope);
      }
    }
    return $this->operator->validateOperation($leftType, $rightType);
  }

  public function run(Scope $scope): Value {
    return $this->operator->operate($this->leftExpression?->run($scope) ?? null, $this->rightExpression?->run($scope) ?? null);
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    if($this->leftExpression !== null && $this->operator->getOperatorType() !== OperatorType::PrefixOperator) {
      $string .= $this->leftExpression->toString($prettyPrintOptions);
    }
    $string .= $this->operator->toString($prettyPrintOptions);
    if($this->rightExpression !== null && $this->operator->getOperatorType() !== OperatorType::PostfixOperator) {
      $string .= $this->rightExpression->toString($prettyPrintOptions);
    }
    return $string;
  }

  public function buildNode(Scope $scope): array {
    if(($this->operator instanceof ImplementableOperator) && $this->operator->getID() !== Operator::IMPLEMENTABLE_DIRECT_ASSIGNMENT) {
      return ['type' => 'Operator','outerType' => $this->validate($scope)->buildNode(),'operator' => $this->operator->getIdentifier(),'leftNode' => $this->leftExpression?->buildNode($scope) ?? null,'rightNode' => $this->rightExpression?->buildNode($scope) ?? null];
    } else {
      throw new \BadMethodCallException('Not implementable operator is not supported by Node system');
    }
  }
}
