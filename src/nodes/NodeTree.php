<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\nodes;

/**
 * @author Timo Lehnertz
 */
class NodeTree {

  public readonly Node $rootNode;

  /**
   * @var array<NodeType>
   */
  public readonly array $nodeTypes;

  /**
   * @var array<NodeInterfaceType>
   */
  public readonly array $nodeInterfaceTypes;

  public readonly array $scope;

  /**
   * @param array<NodeType> $nodeTypes
   * @param array<NodeInterfaceType> $nodeInterfaceTypes
   */
  public function __construct(Node $rootNode, array $nodeTypes, array $nodeInterfaceTypes) {
    $this->rootNode = $rootNode;
    $this->nodeTypes = $nodeTypes;
    $this->nodeInterfaceTypes = $nodeInterfaceTypes;
  }
}
