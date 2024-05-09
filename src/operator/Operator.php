<?php
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\FormulaPart;

/**
 * @author Timo Lehnertz
 */
abstract class Operator implements FormulaPart {

  private readonly OperatorType $type;

  /**
   * precedence of this operator over other operators, lower is higher priority
   * source https://en.cppreference.com/w/cpp/language/operator_precedence
   */
  private readonly int $precedence;

  /**
   * @var bool if true the left expression will be evaluated first when precedence is equal => a+b+c = (a+b)+c
   */
  private readonly bool $associativityLeftToRight;

  public function __construct(OperatorType $type, int $precedence, bool $associativityLeftToRight) {
    $this->precedence = $precedence;
    $this->type = $type;
    $this->associativityLeftToRight = $associativityLeftToRight;
  }

  public function getPrecedence(): int {
    return $this->precedence;
  }

  public function isAssociativityLeftToRight(): bool {
    return $this->associativityLeftToRight;
  }

  public function getType(): OperatorType {
    return $this->type;
  }
}
