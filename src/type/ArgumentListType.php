<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\operator\ImplementableOperator;

/**
 * @author Timo Lehnertz
 */
class ArgumentListType implements Type {

  /**
   * @var array<FunctionArgument>|null
   */
  private ?array $arguments;

  /**
   * @var array<FunctionArgument>
   */
  public function __construct(?array $arguments) {
    $this->arguments = $arguments;
  }

  public function equals(Type $type): bool {
    if(!($type instanceof ArgumentListType)) {
      return false;
    }
    if($this->arguments === null || $type->arguments === null) {
      return true;
    }
    if(count($this->arguments) !== count($type->arguments)) {
      return false;
    }
    for($i = 0;$i < count($type->arguments);$i++) {
      if(!$type->arguments[$i]->equals($this->arguments[$i])) {
        return false;
      }
    }
    return true;
  }

  public function getIdentifier(bool $isNested = false): string {
    if($this->arguments === null) {
      return 'ArgumentList(any)';
    }
    $identifier = '';
    $delimiter = '';
    foreach($this->arguments as $argument) {
      $identifier .= $delimiter.$argument->getIdentifier();
      $delimiter = ',';
    }
    return 'ArgumentList('.$identifier.')';
  }

  public function getOperatorResultType(ImplementableOperator $operator, ?Type $otherType): ?Type {
    return (new ArgumentListValue([], $this))->getOperatorResultType($operator, $otherType);
  }

  public function getCompatibleOperands(ImplementableOperator $operator): array {
    return (new ArgumentListValue([], $this))->getCompatibleOperands($operator);
  }

  public function buildNode(): array {
    throw new \BadMethodCallException('ExpressionListType cant operate');
  }
}
