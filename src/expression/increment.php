<?php
namespace src\expression;

use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\SubFormula;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\expression\Variable;
use TimoLehnertz\formula\procedure\Scope;

class increment implements Expression, Parseable, SubFormula, Nestable {

  public static const TYPE_PRE_INCREMENT = 1;
  public static const TYPE_POST_INCREMENT = 2;
  public static const TYPE_PRE_DECREMENT = 3;
  public static const TYPE_POST_DECREMENT = 4;
  
  private int $type;
  
  private string $variableIdentifier;
  
  private Variable $variable;
  
  public function __construct() {
    
  }
  
  public function calculate() {
    
  }

  public function parse(array &$tokens, int &$index): bool {
    if(sizeof($tokens) < $index + 2) return false;
    $startToken = $tokens[$index];
    $nextToken = $tokens[$index + 1];
    if($startToken->name === '++' && $nextToken->name === 'I') {
      $this->type = self::TYPE_PRE_INCREMENT;
      $this->variableIdentifier = $nextToken->value;
      $index += 2;
      return true;
    }
    if($startToken->name === '--' && $nextToken->name === 'I') {
      $this->type = self::TYPE_PRE_DECREMENT;
      $this->variableIdentifier = $nextToken->value;
      $index += 2;
      return true;
    }
    if($startToken->name === 'I' && $nextToken->name === '++') {
      $this->type = self::TYPE_POST_INCREMENT;
      $this->variableIdentifier = $startToken->value;
      $index += 2;
      return true;
    }
    if($startToken->name === 'I' && $nextToken->name === '--') {
      $this->type = self::TYPE_POST_DECREMENT;
      $this->variableIdentifier = $startToken->value;
      $index += 2;
      return true;
    }
    return false;
  }

  public function getContent(): array {
    return [$variable];
  }
  
  
  public function validate(bool $throwOnError, Scope $scope): bool {
    
  }
  
  public function toString(): string {
    switch($this->type) {
      case self::TYPE_PRE_INCREMENT:
        return '++'.$this->variableIdentifier;
      case self::TYPE_POST_INCREMENT:
        return $this->variableIdentifier.'++';
      case self::TYPE_PRE_DECREMENT:
        return '--'.$this->variableIdentifier;
      case self::TYPE_POST_DECREMENT:
        return $this->variableIdentifier.'--';
      default:
        return '';
    }
  }
}

