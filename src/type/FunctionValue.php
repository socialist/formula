<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;

/**
 * @author Timo Lehnertz
 */
class FunctionValue extends Value {

  /**
   * @var CodeBlock|callable
   */
  private readonly mixed $body;

  private readonly FunctionType $type;

  private readonly Scope $scope;

  public function __construct(CodeBlock|callable $body, FunctionType $type, Scope $scope) {
    $this->body = $body;
    $this->type = $type;
    $this->scope = $scope;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new FunctionValue($this->body, $this->type, $this->scope);
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    if($operator->getID() === Operator::IMPLEMENTABLE_CALL) {
      return [new ArgumentListType($this->type->arguments)];
    } else {
      return [];
    }
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === Operator::IMPLEMENTABLE_CALL) {
      return $this->type->returnType;
    } else {
      return [];
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    var_dump($other);
    if($operator->getID() === Operator::IMPLEMENTABLE_CALL && $other !== null && $other instanceof ArgumentListValue) {
      if(is_callable($this->body)) {
        $args = [];
        $argValues = $other->getValues();
        for($i = 0;$i < count($argValues);$i++) {
          /** @var Value $argValue */
          $argValue = $argValues[$i];
          $expectedType = $this->type->arguments[$i]->type;
          if($argValue->getType()->equals($expectedType)) {
            $args[$i] = $argValue->toPHPValue();
          } else {
            $args[$i] = $argValue->operate(new TypeCastOperator(false, $expectedType), new TypeValue($expectedType))->toPHPValue();
          }
        }
        $phpReturn = call_user_func_array($this->body, $args);
        $formulaReturn = Scope::valueByPHPVar($phpReturn);
        if(!$formulaReturn->getType()->equals($this->type->returnType)) {
          $castedReturnType = $formulaReturn->getOperatorResultType(new TypeCastOperator(false, $this->type->returnType), new TypeValue($this->type->returnType));
          if($castedReturnType === null) {
            throw new FormulaRuntimeException('PHP function returned invalid return value '.$formulaReturn->getType()->getIdentifier().'. Expected '.$this->type->returnType->getIdentifier());
          }
          $formulaReturn = $formulaReturn->operate(new TypeCastOperator(false, $this->type->returnType), new TypeValue($this->type->returnType));
        }
        return $formulaReturn;
      }
    } else {
      throw new FormulaBugException('Invalid operator');
    }
  }

  public function assign(Value $value): void {
    if($value instanceof FunctionValue) {
      $this->body = $value->body;
      $this->scope = $value->scope;
    }
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return 'function';
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('FunctionValue is not supported by node system');
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('FunctionValue list does not have a php representation');
  }
}

