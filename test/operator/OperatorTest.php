<?php
namespace TimoLehnertz\formula\operator;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\FormulaValidationException;

class OperatorTest extends TestCase {

  public function testIncrementPostfix(): void {
    $formula = new Formula('int i=0;print(i++);print(i);return i;');
    $this->expectOutputString('01');
    $result = $formula->calculate();
    $this->assertEquals(1, $result->toPHPValue());
  }

  public function testDecrementPostfix(): void {
    $formula = new Formula('int i=0;print(i--);print(i);return i;');
    $this->expectOutputString('0-1');
    $result = $formula->calculate();
    $this->assertEquals(-1, $result->toPHPValue());
  }

  public function testIncrementPrefix(): void {
    $formula = new Formula('int i=0;print(++i);print(i);return i;');
    $this->expectOutputString('11');
    $result = $formula->calculate();
    $this->assertEquals(1, $result->toPHPValue());
  }

  public function testDecrementPrefix(): void {
    $formula = new Formula('int i=0;print(--i);print(i);return i;');
    $this->expectOutputString('-1-1');
    $result = $formula->calculate();
    $this->assertEquals(-1, $result->toPHPValue());
  }

  public function testNotEquals(): void {
    $formula = new Formula('int i=0;return i!=1;');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());
  }

  public function testGreaterEquals(): void {
    $formula = new Formula('int i=0;return i>=0;');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());

    $formula = new Formula('int i=0;return i>=-1;');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());

    $formula = new Formula('int i=0;return i>=1;');
    $result = $formula->calculate();
    $this->assertEquals(false, $result->toPHPValue());
  }

  public function testLessEquals(): void {
    $formula = new Formula('int i=0;return i<=0;');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());

    $formula = new Formula('int i=0;return i<=1;');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());

    $formula = new Formula('int i=0;return i<=-1;');
    $result = $formula->calculate();
    $this->assertEquals(false, $result->toPHPValue());
  }

  public function testImplicitTypeCastOperator(): void {
    $formula = new Formula('false || 1');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());
    $this->assertEquals('false||1', $formula->prettyprintFormula());
  }

  public function testExplicitTypeCastOperator(): void {
    $formula = new Formula('false || (boolean)1');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());
    $this->assertEquals('false||(boolean)1', $formula->prettyprintFormula());
  }

  public function testOrderOfMathematicOperators(): void {
    $formula = new Formula('1+5*4-3/6+2');
    $result = $formula->calculate();
    $this->assertEquals(1 + 5 * 4 - 3 / 6 + 2, $result->toPHPValue());
  }

  public function testUnaryPlus(): void {
    $formula = new Formula('1<=+3');
    $result = $formula->calculate();
    $this->assertEquals(true, $result->toPHPValue());
  }

  public function testPrecedence(): void {
    for($i = 0;$i < 5;$i++) {
      $formula = new Formula('int a = '.$i.'; return -1>=2^1<=+3^3!=3^min(1,2)^a++<a^++a<a^a--<a^--a<a^(a+=1+1)!=a;');
      $result = $formula->calculate();
      $a = $i;
      $this->assertEquals(-1 >= 2 xor 1 <= +3 xor 3 != 3 xor min(1, 2) xor $a++ < $a xor ++$a < $a xor $a-- < $a xor --$a < $a xor ($a += 1 + 1) != $a, $result->toPHPValue());
    }
  }

  public function testDoubleIncrement(): void {
    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('1:10 Validation error: Can\'t assign final value');
    new Formula('int a = 0; return ++a++;');
  }

  public function testConstants(): void {
    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('1:0 Validation error: Can\'t assign final value');
    new Formula('1 = 0;');
  }

  public function testConstants2(): void {
    $this->expectException(FormulaValidationException::class);
    $this->expectExceptionMessage('1:10 Validation error: Can\'t assign final value');
    new Formula('int a = 0; return a = a = a;');
  }
}
