<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class FunctionTypeParserTest extends TestCase {

  public function testOK(): void {
    //     new Formula('function(int) -> int a = (int a) -> a;');
    $formula = new Formula('int a = 0;function(int... args) -> float vargFunc = (int... args) -> sum(args); return vargFunc(1,2,3);');
    $this->assertEquals(6, $formula->calculate()->toPHPValue());
  }
}
