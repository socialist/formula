<?php
namespace TimoLehnertz\formula\type;

use TimoLehnertz\formula\type\Type;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class ImplementedOperator {

  /**
   * The type of operator
   */
  public readonly int $operator;

  /**
   * The allowed input types
   *
   * @var Type[]
   */
  public readonly array $types;
}

