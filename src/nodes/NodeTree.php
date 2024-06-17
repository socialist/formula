<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\nodes;

/**
 * @author Timo Lehnertz
 */
class NodeTree {

  public readonly Node $rootNode;

  public readonly NodeTreeScope $scope;

  public function __construct(Node $rootNode, NodeTreeScope $scope) {
    $this->rootNode = $rootNode;
    $this->scope = $scope;
  }
}
