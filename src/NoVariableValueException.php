<?php
namespace TimoLehnertz\formula;

class NoVariableValueException extends \Exception {
  
  private string $missingVariable;
  
  public function __construct(string $message, string $missingVariable = '') {
    parent::__construct($message);
    $this->missingVariable = $missingVariable;
  }
  
  public function getMissingVariable(): string {
    return $this->missingVariable;
  }
}
