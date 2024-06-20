<?php
namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;

class ArrayTest extends TestCase {

  public function testIntegration(): void {
    $source = file_get_contents('test/type/arrays.formula');
    $formula = new Formula($source);
    $result = $formula->calculate();
    $this->assertEquals(25, $result->toPHPValue());
  }

  //   public function testEquals(): void {
  //     $scope = new Scope();
  //     $scope->definePHP(false, 'scope1', $scope);
  //     $scope->definePHP(false, 'scope2', new Scope());
  //     $formula = new Formula('scope1 == scope1', $scope);
  //     $this->assertEquals(true, $formula->calculate()->toPHPValue());
  //     $formula = new Formula('scope1 == scope2', $scope);
  //     $this->assertEquals(false, $formula->calculate()->toPHPValue());
  //   }
}
