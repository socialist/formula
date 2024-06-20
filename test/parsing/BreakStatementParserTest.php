<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\parsing\ParsingException;

class BreakStatementParserTest extends TestCase {

  public function testOK(): void {
    $formula = new Formula('do {break;} while(true);return 1;');
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
  }

  public function testEndOfInput1(): void {
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage('Syntax error in break statement: 1:10 } . Message: Unexpected token. Expected ;');
    new Formula('do {break} while(true);return 1;');
  }
}
