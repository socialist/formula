<?php
namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;

class CompoundTypeTest extends TestCase {

  public function funcTest(): int|bool {
    return false;
  }

  public function testTruthy(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'func', [$this,'funcTest']);
    $formula = new Formula('var a = func(); a = 0; return a;', $scope);
    $this->assertEquals(0, $formula->calculate()->toPHPValue());
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
