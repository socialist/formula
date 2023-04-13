<?php
/**
 * @author Timo Lehnertz
 */
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\NullpointerException;
use TimoLehnertz\formula\operator\Calculateable;

class NullExpression implements Calculateable {
  
  public function __construct() {
    
  }
  
  private function throw(): void {
    throw new NullpointerException('Tried to calculate on null');
  }
  
  public function add(Calculateable $summand): Calculateable {
    $this->throw();
  }

  public function getValue() {
    $this->throw();
  }

  public function isTruthy(): bool {
    $this->throw();
  }

  public function subtract(Calculateable $difference): Calculateable {
    $this->throw();
  }

  public function pow(Calculateable $power): Calculateable {
    $this->throw();
  }

  public function divide(Calculateable $divisor): Calculateable {
    $this->throw();
  }

  public function calculate(): Calculateable {
    $this->throw();
  }

  public function multiply(Calculateable $factor): Calculateable {
    $this->throw();
  }
}

