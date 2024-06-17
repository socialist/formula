<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\nodes;

/**
 * @author Timo Lehnertz
 */
class NodeTreeScope {

  public readonly ?NodeTreeScope $parent;

  /**
   * @var array<string, NodeInterfaceType>
   */
  public readonly array $definedValues;

  /**
   * @param array<NodeInterfaceType> $inputs
   */
  public function __construct(?NodeTreeScope $parent, array $definedValues) {
    $this->parent = $parent;
    $this->definedValues = $definedValues;
  }
}
