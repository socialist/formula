<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\operator\ArrayAccessOperator;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\operator\Operator;

/**
 * @author Timo Lehnertz
 */
class FunctionValue extends Value {

  private readonly CodeBlock|callable $body;

  /**
   * @var array<FunctionArgument>
   */
  private readonly array $arguments;

  private readonly Type $returnType;

  private readonly Scope $scope;

  /**
   * @param array<FunctionArgument> $arguments
   */
  public function __construct(CodeBlock|callable $body, array $arguments, Type $returnType, Scope $scope) {
    $this->body = $body;
    $this->arguments = $arguments;
    $this->scope = $scope;
  }

  public function getType(): Type {
    return $this->type;
  }

  public function isTruthy(): bool {
    return true;
  }

  public function copy(): ArrayValue {
    return new ArrayValue($this->value);
  }

  public function valueEquals(Value $other): bool {
    return $other === $this;
  }

  protected function getValueExpectedOperands(ImplementableOperator $operator): array {
    if($operator->id === Operator::IMPLEMENTABLE_CALL) {
      return [new ExpressionListType($expressionTypes)];
    } else {
      return [];
    }
  }

  protected function getValueOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    if($operator->id === Operator::IMPLEMENTABLE_CALL) {
      return $this->returnType;
    } else {
      return [];
    }
  }

  protected function valueOperate(ImplementableOperator $operator, ?Value $other): Value {
    if($operator instanceof ArrayAccessOperator) {
      if($other instanceof IntegerValue) {
        $key = $other->getValue();
      }
      if($other instanceof FloatValue) {
        $key = $other->getValue();
      }
      if($other instanceof StringValue) {
        $key = $other->getValue();
      }
      if(defined($this->value[$key])) {
        return $this->value[$key];
      } else {
        throw new \BadFunctionCallException('Array key does not exist!');
      }
    } else if($operator->id === ImplementableOperator::TYPE_EQUALS) {
      return new BooleanValue($this === $other);
    } else {
      throw new \BadFunctionCallException('Invalid operator!');
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
}

