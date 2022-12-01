<?php
namespace TimoLehnertz\formula\expression;

use InvalidArgumentException;
use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\tokens\Token;


/**
 *
 * @author Timo Lehnertz
 *        
 */
class StringLiteral implements Calculateable {

  /**
   *
   * @readonly
   * @var string
   */
  private string $string;

  /**
   * @param string $string
   */
  private function construct(string $string) {
    $this->string = $string;
//     echo "constructed String litearl: $string";
  }

  public static function fromToken(Token $token): Calculateable {
    $string = $token->value;
    if(substr($token->value, 0, 1) == "\"") {
      $string = str_replace("\"", '', $string); // exclude ""
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

  public function add(Calculateable $summand) {
    throw new InvalidArgumentException("Cant add strings");
  }

  public function subtract(Calculateable $difference) {
    throw new InvalidArgumentException("Cant subtract strings");
  }

  public function multiply(Calculateable $factor) {
    throw new InvalidArgumentException("Cant multiply strings");
  }

  public function divide(Calculateable $divisor) {
    throw new InvalidArgumentException("Cant divide strings");
  }

  public function pow(Calculateable $power) {
    throw new InvalidArgumentException("Cant multiply strings");
  }
  
  public function getValue() {
    return $this->string;
  }

  public function calculate(): Calculateable {
    return $this;
  }
  
  public function isTruthy(): bool {
    return true; // always truthy
  }

}
