<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\nodes\Node;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\operator\TypeCastOperator;

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
      //       case OperatorType::PostfixOperator:
      //         assertNotNull($leftExpression, 'PostfixOperator requires a left expression');
      //         assertNull($rightExpression, 'PostfixOperator can\'t have a right expression');
      //         break;
    }
  }

  public function validate(Scope $scope): Type {
    $leftType = $this->leftExpression?->validate($scope) ?? null;
    $rightType = $this->rightExpression?->validate($scope) ?? null;
    $returnType = null;
    switch($this->operator->getOperatorType()) {
      case OperatorType::PrefixOperator:
        $returnType = $rightType->getOperatorResultType($this->operator, null);
        break;
      case OperatorType::InfixOperator:
        $operands = $leftType->getCompatibleOperands($this->operator);
        if(count($operands) === 0) {
          throw new FormulaValidationException($leftType->toString(PrettyPrintOptions::buildDefault()).' does not implement operator '.$this->operator->toString(PrettyPrintOptions::buildDefault()));
        }
        $this->rightExpression = OperatorExpression::castExpression($this->rightExpression, $rightType, CompoundType::buildFromTypes($operands, false), $scope, $this);
        $rightType = $this->rightExpression->validate($scope);
        $returnType = $leftType->getOperatorResultType($this->operator, $rightType);
        break;
      //       case OperatorType::PostfixOperator:
      //         $returnType = $leftType->getOperatorResultType($this->operator, null);
      //         break;
    }
    if($returnType === null) {
      throw new FormulaValidationException('Invalid operation '.($leftType?->getIdentifier() ?? '').' '.$this->operator->toString(PrettyPrintOptions::buildDefault()).' '.($rightType?->getIdentifier() ?? ''));
    }
    return $returnType;
  }

  public function run(Scope $scope): Value {
    switch($this->operator->getOperatorType()) {
      case OperatorType::PrefixOperator:
        return $this->rightExpression->run($scope)->operate($this->operator, null);
      case OperatorType::InfixOperator:
        return $this->leftExpression->run($scope)->operate($this->operator, $this->rightExpression->run($scope));
      //       case OperatorType::PostfixOperator:
      //         return $this->leftExpression->run($scope)->operate($this->operator, null);
      //         break;
      default:
        throw new FormulaBugException('Invalid operatorType');
    }
  }

  public static function castExpression(Expression $source, Type $sourceType, Type $targetType, Scope $scope, FormulaPart $context): Expression {
    if($targetType->assignableBy($sourceType, true)) {
      return $source;
    } else {
      if($source instanceof CastableExpression) {
        return $source->getCastedExpression($targetType, $scope);
      }
      $castableTypes = $sourceType->getCompatibleOperands(new ImplementableOperator(ImplementableOperator::TYPE_TYPE_CAST));
      /** @var TypeType $castableType */
      foreach($castableTypes as $castableType) {
        $castableType = $castableType->getType();
        if($targetType->assignableBy($castableType, true)) {
          $expression = (new TypeCastOperator(false, $castableType))->transform(null, $source);
          return $expression;
        }
      }
      throw new FormulaValidationException('Unable to convert '.$sourceType->getIdentifier().' to '.$targetType->getIdentifier());
    }
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

  public function buildNode(Scope $scope): Node {
    $connected = [];
    if($this->leftExpression !== null) {
      $connected[] = $this->leftExpression->buildNode($scope);
    }
    if($this->rightExpression !== null) {
      $connected[] = $this->rightExpression->buildNode($scope);
    }
    return new Node('OperatorExpression', $connected, ['operator' => $this->operator->getID()]);
  }
}
