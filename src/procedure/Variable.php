<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 *
 * @author Timo Lehnertz
 *        
 */
class Variable {

  private string $identifier;

  private Type $type;

  private ?Value $value;

  public function __construct(string $identifier, Type $type, ?Value $value) {
    $this->identifier = $identifier;
    $this->type = $type;
    $this->value = $value;
  }

  public function getIdentifier(): string {
    return $this->identifier;
  }

  public function getVaue(): ?Value {
    return $this->value;
  }

  public function assign(Value $value): void {
    if($value->getType()
      ->getIdentifier() === $this->type->getIdentifier()) {
      $this->value->assign($value);
    } else {
      throw new \BadFunctionCallException('cant set assign \"'.$this->identifier.'\" with value of type '.$value->getType()
        ->getIdentifier().'. Expected '.$this->type->getIdentifier());
    }
  }
}

