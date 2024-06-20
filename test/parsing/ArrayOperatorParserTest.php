<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\parsing\ParsingException;

class ArrayOperatorParserTest extends TestCase {

  public function testEndOfInput1(): void {
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage('Syntax error in array operator: 1:2 [ . Message: Unexpected end of input');
    new Formula('a[');
  }

  public function testEndOfInput2(): void {
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage('Syntax error in constant expression: 1:4 , . Message: Unexpected token. Expected ]');
    new Formula('a[1,');
  }

  public function testEndOfInput3(): void {
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage('Syntax error in constant expression: 1:1 1 . Message: Unexpected end of input');
    new Formula('a[1');
  }
}
