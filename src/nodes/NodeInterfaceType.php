<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\nodes;

/**
 * @author Timo Lehnertz
 */
class NodeInterfaceType {

  public readonly string $name;

  public readonly array $additionalInfo;

  public function __construct(string $name, array $additionalInfo = []) {
    $this->name = $name;
    $this->additionalInfo = $additionalInfo;
  }
}
