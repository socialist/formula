<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\nodes;

/**
 * @author Timo Lehnertz
 */
class Node {

  public readonly string $nodeType;

  /**
   * @var array<int, Node>
   */
  public readonly array $connectedInputs;

  public readonly array $info;

  /**
   * @param array<int, Node> $connectedInputs
   */
  public function __construct(string $nodeType, array $connectedInputs, array $info = []) {
    $this->nodeType = $nodeType;
    $this->connectedInputs = $connectedInputs;
    $this->info = $info;
  }
}
