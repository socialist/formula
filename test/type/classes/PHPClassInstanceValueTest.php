<?php
namespace test\type\classes;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;

class PHPClassInstanceValueTest extends TestCase {

  public function testTruthy(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'scope', $scope);
    $formula = new Formula('scope || false', $scope);
    $this->assertEquals(true, $formula->calculate()->toPHPValue());
  }

  public function testEquals(): void {
    $scope = new Scope();
    $scope->definePHP(false, 'scope1', $scope);
    $scope->definePHP(false, 'scope2', new Scope());
    $formula = new Formula('scope1 == scope1', $scope);
    $this->assertEquals(true, $formula->calculate()->toPHPValue());
    $formula = new Formula('scope1 == scope2', $scope);
    $this->assertEquals(false, $formula->calculate()->toPHPValue());
  }
}
