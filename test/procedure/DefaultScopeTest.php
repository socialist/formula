<?php
namespace test\procedure;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use const false;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\NeverType;
use TimoLehnertz\formula\ExitIfNullException;

class DefaultScopeTest extends TestCase {

  public function functionProvider(): array {
    // @formatter:off
    return [
      ["print('Hello world!')", new VoidType(), null, 'Hello world!'],
      ["println('Hello world!')", new VoidType(), null, 'Hello world!'.PHP_EOL],
      ["pow(5,2)", new FloatType(), 25, null],
      ["min(5,6,7)", new FloatType(), 5, null],
      ["max(5,6,7)", new FloatType(), 7, null],
      ["sqrt(25)", new FloatType(), 5, null],
      ["ceil(5.3)", new FloatType(), 6, null],
      ["floor(5.3)", new FloatType(), 5, null],
      ["round(5.5)", new FloatType(), 6, null],
      ["sin(0)", new FloatType(), 0, null],
      ["cos(PI)", new FloatType(), -1, null],
      ["tan(0)", new FloatType(), 0, null],
      ["is_nan(0)", new BooleanType(), false, null],
      ["abs(-10)", new FloatType(), 10, null],
      ["asVector(1,2,3)", new ArrayType(new MixedType(), new MixedType()), [1,2,3], null],
      ["sizeof({1,2,3})", new IntegerType(), 3, null],
      ["inRange(0,0,10)", new BooleanType(), true, null],
      ["inRange(-1,0,10)", new BooleanType(), false, null],
      ["reduce({1,2,3},{1,2,4})", new ArrayType(new IntegerType(), new IntegerType()), [1,2], null],
      ["firstOrNull({1,2,3})", CompoundType::buildFromTypes([new NullType(), new IntegerType()]), 1, null],
      ["firstOrNull({1.5,2,3})", CompoundType::buildFromTypes([new NullType(), new IntegerType(), new FloatType()]), 1.5, null],
      ["firstOrNull({null})", new NullType(), null, null],
      ["firstOrNull({})", new NullType(), null, null],
      ["assertTrue(true)", new VoidType(), null, null],
      ["assertFalse(false)", new VoidType(), null, null],
      ["assertEquals(6,1+2+3)", new VoidType(), null, null],
      ["sum({1,{{{2}},3},4}, 5, {6,7+8+9})", new FloatType(), 45, null],
      ["sizeof({1,{{{2}},3},4}, 5, {6,7+8+9})", new IntegerType(), 7, null],
      ["avg({1,{{{2}},3},4}, 5, {6,7+8+9})", new FloatType(), 45.0 / 7, null],

      ["array_filter({1,2,3,4}, (int a) -> a % 2 == 0)", new ArrayType(new IntegerType(), new IntegerType()), [1 => 2, 3 => 4], null],
      ["array_filter({1,2,3,4}, boolean(int a) {return a % 2 == 0;})", new ArrayType(new IntegerType(), new IntegerType()), [1 => 2, 3 => 4], null],

      ["PI", new FloatType(), M_PI, null],
    ];
    // @formatter:on
  }

  /**
   * @dataProvider functionProvider
   */
  public function testFunctions(string $source, Type $expectedReturnType, mixed $expectedReturn, ?string $expectedOutput): void {
    $formula = new Formula($source);
    if($expectedOutput !== null) {
      $this->expectOutputString($expectedOutput);
    }
    if(!$expectedReturnType->assignableBy($formula->getReturnType())) {
      var_dump($formula->getReturnType());
    }
    $this->assertTrue($expectedReturnType->equals($formula->getReturnType()));
    $result = $formula->calculate();
    if(($expectedReturnType instanceof VoidType)) {
      $this->assertInstanceOf(VoidValue::class, $result);
    } else {
      $this->assertEquals($expectedReturn, $result->toPHPValue());
    }
  }

  public function testEarlyReturnException(): void {
    $formula = new Formula('earlyReturnIfNull(null)');
    $this->assertInstanceOf(NeverType::class, $formula->getReturnType());
    $this->expectException(ExitIfNullException::class);
    $formula->calculate();
  }

  public function testEarlyReturn(): void {
    $formula = new Formula('earlyReturnIfNull({1,false,null}[0])');
    $this->assertTrue(CompoundType::buildFromTypes([new IntegerType(),new BooleanType()])->equals($formula->getReturnType()));
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
  }

  public function testEarlyReturnNormalType(): void {
    $formula = new Formula('earlyReturnIfNull(1)');
    $this->assertInstanceOf(IntegerType::class, $formula->getReturnType());
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
  }
}
