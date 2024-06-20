<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

/**
 * @author Timo Lehnertz
 */
class PrettyPrintOptions {

  private int $indentations = 0;

  public int $charsPerIndent = 2;

  public bool $indentationMethodSpaces = true;

  public string $newLine = "\r\n";

  public function __construct() {}

  public static function buildDefault(): PrettyPrintOptions {
    return new PrettyPrintOptions();
  }

  public function indent(): void {
    $this->indentations++;
  }

  public function outdent(): void {
    $this->indentations--;
  }

  public function getIndentStr(): string {
    $str = '';
    for($i = 0;$i < $this->indentations * $this->charsPerIndent;$i++) {
      if($this->indentationMethodSpaces) {
        $str .= ' ';
      } else {
        $str .= "\t";
      }
    }
    return $str;
  }
}

