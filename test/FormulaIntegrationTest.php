<?php
namespace test;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class FormulaIntegrationTest extends TestCase {

  public function testPrim(): void {
    $source = file_get_contents('test/prim.formula');
    $formula = new Formula($source);
    $result = $formula->calculate();
    $this->assertEquals(25, $result->toPHPValue());
  }
}
