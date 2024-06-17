<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\CompoundType;
use function PHPUnit\Framework\assertTrue;

/**
 * @author Timo Lehnertz
 *
 *         Readonly class containing information about the termination of a statement
 */
class StatementReturnType {

  /**
   * If this statement includes a return statement this field holds its return type or Void
   * If there is no return statement in reach this field is null
   */
  public readonly ?Type $returnType;

  /**
   * How likely a continue, break, or return statement will be hit
   */
  public readonly Frequency $stopFrequency;

  /**
   * How likely a return statement will be hit
   */
  public readonly Frequency $returnFrequency;

  public function __construct(?Type $returnType, Frequency $stopFrequency, Frequency $returnFrequency) {
    $this->returnType = $returnType;
    $this->stopFrequency = $stopFrequency;
    $this->returnFrequency = $returnFrequency;
    assertTrue(!($returnFrequency > $stopFrequency), 'Return frequency can\'t be higher than stop frequency');
    assertTrue(!($returnType === null && $returnFrequency !== Frequency::NEVER), 'Return type can\'t be null here');
  }

  /**
   * Example if(a){ return x;} else { return x; }
   */
  public function concatOr(StatementReturnType $other): StatementReturnType {
    $returnTypes = [];
    if($this->returnType !== null) {
      $returnTypes[] = $this->returnType;
    }
    if($other->returnType !== null) {
      $returnTypes[] = $other->returnType;
    }
    if(count($returnTypes) > 0) {
      $returnType = CompoundType::buildFromTypes($returnTypes, false);
    } else {
      $returnType = null;
    }
    return new StatementReturnType($returnType, Frequency::or($this->stopFrequency, $other->stopFrequency), Frequency::or($this->returnFrequency, $other->returnFrequency));
  }

  /**
   * Example: if(a) { return x; } return y;
   */
  public function concatSequential(StatementReturnType $other): StatementReturnType {
    if($this->stopFrequency === Frequency::ALWAYS) {
      return $this;
    }
    $returnTypes = [];
    if($this->returnType !== null) {
      $returnTypes[] = $this->returnType;
    }
    if($other->returnType !== null) {
      $returnTypes[] = $other->returnType;
    }
    if(count($returnTypes) > 0) {
      $returnType = CompoundType::buildFromTypes($returnTypes, false);
    } else {
      $returnType = null;
    }
    return new StatementReturnType($returnType, Frequency::getHigher($this->stopFrequency, $other->stopFrequency), Frequency::getHigher($this->returnFrequency, $other->returnFrequency));
  }
}
