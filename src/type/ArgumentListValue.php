<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\operator\TypeCastOperator;
use TimoLehnertz\formula\FormulaBugException;

/**
 * @author Timo Lehnertz
 */
class ArgumentListValue extends Value {

  /**
   * @var array<Value>
   */
  private readonly array $values;

  private readonly ArgumentListType $type;

  /**
   * @param array<Value>
   */
  public function __construct(array $values, ArgumentListType $type) {
    $this->values = $values;
    $this->type = $type;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new ArgumentListValue($this->values, $this->type);
  }

  public function valueEquals(Value $other): bool {
    if(!($other instanceof ArgumentListValue)) {
      return false;
    }
    if(count($this->values) !== count($other->values)) {
      return false;
    }
    foreach($this->values as $i => $value) {
      if(!$this->values[$i]->equals($value->values[$i])) {
        return false;
      }
    }
    return true;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST) {
      return [new TypeType(new ArgumentListType(null))];
    } else {
      return [];
    }
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST) {
      if(($otherType instanceof TypeType) && ($otherType->getType() instanceof ArgumentListType)) {
        // validate compatibility
        if(count($otherType->getType()->arguments) > count($this->type->arguments)) {
          return null; // other type has too many arguments
        }
        for($i = 0;$i < count($this->type->arguments);$i++) {
          /** @var FunctionArgument $ownArgument */
          $ownArgument = $this->type->arguments[$i];
          $otherArgument = null;
          if(isset($otherType->getType()->arguments[$i])) {
            $otherArgument = $otherType->getType()->arguments[$i];
          }
          if($otherArgument === null) {
            if($ownArgument->optional) {
              break;
            } else {
              return null;
            }
          }
          if($ownArgument->type->equals($otherArgument)) {
            continue;
          }
          // check if cast is possible
          /** @var FunctionArgument $otherArgument */
          if($otherArgument->type->getOperatorResultType(new TypeCastOperator(false, $ownArgument->type), new TypeType($ownArgument->type)) === null) {
            return null;
          }
        }
      }
    }
    return null;
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator->getID() === Operator::IMPLEMENTABLE_TYPE_CAST) {
      if(($other instanceof TypeValue) && ($other->getType() instanceof ArgumentListType)) {
        $values = [];
        for($i = 0;$i < count($this->values);$i++) {
          $expectedType = $other->getType()->arguments[$i];
          /** @var Value $ownValue */
          if($expectedType->equals($ownValue->getType())) {
            $values[$i] = $this->values[$i];
          } else {
            $values[$i] = $ownValue->operate(new TypeCastOperator(false, $expectedType), new TypeType($expectedType));
          }
        }
        return new ArgumentListValue($values, $other->getType());
      }
    }
  }

  public function getValues(): array {
    return $this->values;
  }

  public function assign(Value $value): void {
    throw new \BadMethodCallException('ExpressionListValue cant operate');
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $string = '';
    $del = '';
    /** @var Expression $value */
    foreach($this->value as $value) {
      $string .= $del.$value->toString($prettyPrintOptions);
      $del = ',';
    }
    return $string;
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('ArgumentListValue can\'t build nodes');
  }

  public function toPHPValue(): mixed {
    throw new FormulaBugException('ArgumentListValue does not have a php representation');
  }

  public function toStringValue(): StringValue {
    return new StringValue('ArgumentListValue');
  }
}
