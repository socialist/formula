<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class ParserIntegrationTest extends TestCase {

  public function testOK(): void {
    $source = file_get_contents('test/parsing/parsingTest.formula');
    $formula = new Formula($source);
    $result = $formula->calculate();
    $this->assertEquals(37, $result->toPHPValue());
    $parsedSource = $formula->getFormula();

    $formula = new Formula($parsedSource);
    $this->assertEquals(37, $result->toPHPValue()); // assert that stringify works as expected
  }
}
