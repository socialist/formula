<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\FormulaValidationException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\OperatorType;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\FormulaPartMetadate;

/**
 * @author Timo Lehnertz
 */
class OperatorExpression extends Expression {

  public readonly ?Expression $leftExpression;

  public readonly ImplementableOperator $operator;

  public ?Expression $rightExpression;

  //   private readonly \Exception $e;
  public function __construct(?Expression $leftExpression, ImplementableOperator $operator, ?Expression $rightExpression) {
    parent::__construct();
    $this->leftExpression = $leftExpression;
    $this->operator = $operator;
    $this->rightExpression = $rightExpression;

    //     try {
    //       throw new \Exception("Moin");
    //     } catch(\Exception $e) {
    //       $this->e = $e;
    //     }

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
        assertNotNull($leftExpression, 'PostfixOperator requires a left expression');
        assertNull($rightExpression, 'PostfixOperator can\'t have a right expression');
        break;
    }
  }

  public function validateStatement(Scope $scope): Type {
    //     if(FormulaPartMetadate::get($this) === null) {
    //       throw $this->e;
    //     }
    $leftType = $this->leftExpression?->validate($scope) ?? null;
    $rightType = $this->rightExpression?->validate($scope) ?? null;
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
      case OperatorType::PostfixOperator:
        $returnType = $leftType->getOperatorResultType($this->operator, null);
        break;
      default:
        throw new FormulaBugException('Invalid operatorType');
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
        break;
      case OperatorType::InfixOperator:
        return $this->leftExpression->run($scope)->operate($this->operator, $this->rightExpression->run($scope));
        break;
      case OperatorType::PostfixOperator:
        return $this->leftExpression->run($scope)->operate($this->operator, null);
        break;
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
          $expression = new OperatorExpression($source, new ImplementableOperator(ImplementableOperator::TYPE_TYPE_CAST), new TypeExpression($castableType));
          FormulaPartMetadate::copy($context, $expression);
          return $expression;
        }
      }
      throw new FormulaValidationException($context, 'Unable to convert '.$sourceType->getIdentifier().' to '.$targetType->getIdentifier());
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

  public function buildNode(Scope $scope): array {
    return ['type' => 'Operator','outerType' => $this->validate($scope)->buildNode(),'operator' => $this->operator->getIdentifier(),'leftNode' => $this->leftExpression?->buildNode($scope) ?? null,'rightNode' => $this->rightExpression?->buildNode($scope) ?? null];
  }
}
