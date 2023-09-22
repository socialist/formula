<?php
namespace TimoLehnertz\formula;

class PrettyPrintOptions {
  
  private string $statementSeperator;
  
  public function __construct() {
    $this->statementSeperator = PHP_EOL;
  }
  
  public function getStatementSeperator(): string {
    return $this->statementSeperator;
  }
}

