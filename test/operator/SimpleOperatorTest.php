<?php
namespace TimoLehnertz\formula\operator;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\PrettyPrintOptions;

class SimpleOperatorTest extends TestCase {

  public function testBasic(): void {
    $plusOperator = new SimpleOperator(SimpleOperator::TYPE_ADDITION);
    $this->assertEquals('+', $plusOperator->toString(new PrettyPrintOptions()));
  }
}
