<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class ParserIntegrationTest extends TestCase {

  public function testOK(): void {
    $source = file_get_contents('test/parsing/parsingTest.formula');
    $formula = new Formula($source);
    $result = $formula->calculate();
    $this->assertEquals(40, $result->toPHPValue());
    $parsedSource = $formula->prettyprintFormula();
    //     echo $parsedSource;
    $formula = new Formula($parsedSource);
    $this->assertEquals(40, $result->toPHPValue()); // assert that stringify works as expected
  }
}
