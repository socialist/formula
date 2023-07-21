<?php
namespace TimoLehnertz\formula\types;

abstract class Type {
  
  private bool $isArray;
  
  private string $name;
  
  public function __construct(bool $isArray, string $name) {
    $this->isArray = $isArray;
    $this->name = $name . $isArray ?  '[]' : '';
  }
  
  public abstract function isAssignableWith(Type $type): bool;
}

