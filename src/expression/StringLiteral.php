<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\tokens\Token;
use InvalidArgumentException;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class StringLiteral implements Calculateable, SubFormula {

  /**
   *
   * @readonly
   * @var string
   */
  private string $string;

  /**
   * @param string $string
   */
  private function __construct(string $string) {
    $this->string = $string;
//     echo "constructed String litearl: $string";
  }

  public static function fromToken(Token $token): Calculateable {
    $string = $token->value;
    if(substr($token->value, 0, 1) == '"') {
      $string = str_replace('"', '', $string); // exclude ""
    } else if(substr($token->value, 0, 1) == "'") {
      $string = str_replace("'", '', $string); // exclude ''
    }
    return StringLiteral::fromString($string);
  }
  
  public static function fromString(string $string): Calculateable {
    $timeLiteral = TimeLiteral::fromString($string);
    if($timeLiteral != null) return $timeLiteral;

    $intervalLiteral = TimeIntervalLiteral::fromString($string);
    if($intervalLiteral != null) return $intervalLiteral;
    
    return new StringLiteral($string);
  }

  /**
   * String concatination could pose security risks in some application
   * 
   * {@inheritDoc}
   * @see \TimoLehnertz\formula\operator\Calculateable::add()
   */
  public function add(Calculateable $summand): Calculateable {
    throw new InvalidArgumentException("Cant add strings");
//   	if(!($summand instanceof StringLiteral)) throw new InvalidArgumentException("Cant add strings");
//   	return new StringLiteral($this->string.$summand->string);
  }

  public function subtract(Calculateable $difference): Calculateable {
    throw new InvalidArgumentException("Cant subtract strings");
  }

  public function multiply(Calculateable $factor): Calculateable {
    throw new InvalidArgumentException("Cant multiply strings");
  }

  public function divide(Calculateable $divisor): Calculateable {
    throw new InvalidArgumentException("Cant divide strings");
  }

  public function pow(Calculateable $power): Calculateable {
    throw new InvalidArgumentException("Cant multiply strings");
  }
  
  public function getValue(): string {
    return $this->string;
  }

  /**
   * @param string $value
   */
  public function setValue(string $value): void {
    $this->string = $value;
  }
  
  public function calculate(): Calculateable {
    return $this;
  }
  
  public function isTruthy(): bool {
    return true; // strings are always truthy
  }

  public function toString(): string {
    return "'$this->string'";
  }
}