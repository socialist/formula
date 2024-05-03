<?php
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * Readonly class containing information about the termination of a statement
 * Will get passed back from the run method of Statements
 *
 * @author Timo Lehnertz
 *        
 */
class StatementValue implements Value {

  /**
   * Null if no ReturnStatement was called
   * Locator íf a ReturnStatement was called containing the returned value
   */
  private readonly Value $returnValue;

  /**
   * Null if no continue statement was called
   * int the number of continued cycles otherwise
   */
  private readonly ?int $continueCount;

  /**
   * Indicates wether a break statement was executd
   */
  private readonly bool $breakFlag;

  private function __construct(Value $returnValue, ?int $continueCount = null, bool $breakFlag = false) {
    $this->returnValue = $returnValue;
    $this->continueCount = $continueCount;
    $this->breakFlag = $breakFlag;
  }

  public function getType(): Type {
    return $this->returnValue->getType();
  }

  public function toString(): string {
    return $this->returnValue->toString();
  }

  public function assign(self $value): void {
    throw new \BadFunctionCallException('Can\'t assign the result of a statement');
  }
}

