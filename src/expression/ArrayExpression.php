<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 *
 * @author Timo Lehnertz
 */
class ArrayExpression implements Expression {

  /**
   *
   * @var array<Expression>
   */
  private array $elements = [];

  /**
   *
   * @param array<Expression> $elements
   */
  public function __construct(array $elements) {
    $this->elements = $elements;
  }

  public function run(): Value {}

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    $str = '';
    $del = '';
    /** @var Expression $element */
    foreach($this->elements as $element) {
      $str .= $del.$element->toString($prettyPrintOptions);
      $del = ',';
    }
    return '('.$str.')';
  }

  public function getSubParts(): array {
    return $this->elements;
  }

  public function validate(Scope $scope): Type {}
}