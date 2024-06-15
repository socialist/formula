<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class FormulaPartException extends \Exception {

  private readonly FormulaPart $formulaPart;

  private readonly ?FormulaPartMetadate $metadata;

  public function __construct(FormulaPart $formulaPart, string $exceptionType, string $message) {
    $this->formulaPart = $formulaPart;
    $this->metadata = FormulaPartMetadate::get($formulaPart);
    if($this->metadata !== null) {
      $message = $exceptionType.' at '.($this->metadata->firstToken->line + 0).':'.($this->metadata->firstToken->position + 1).' '.$this->metadata->getSource(5).' Message: '.$message;
    } else {
      $message = $exceptionType.': '.$message;
    }
    parent::__construct($message);
  }
}
