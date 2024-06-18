<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class CodeBlockParserTest extends TestCase {

  public function testSimokeBlock(): void {
    $formula = new Formula('int i = 0;{i++;}return i;'); // test that {i++;} is not picked up as array
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
  }
}
