<?php
namespace test\procedure;

use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertInstanceOf;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\nodes\NodeTreeScope;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\classes\ClassType;
use TimoLehnertz\formula\type\classes\FieldType;
use TimoLehnertz\formula\type\classes\PHPClassInstanceValue;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use const false;

class ScopeTest extends TestCase {

  public function testDefine(): void {
    $scope = new Scope();
    $this->assertFalse($scope->isDefined('i'));
    $scope->definePHP(false, 'i', 0);
    $this->assertTrue($scope->isDefined('i'));
  }

  public function testRedefine(): void {
    $scope = new Scope();
    $scope->definePHP(false, 'i', 0);
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Can\'t redefine i');
    $scope->definePHP(false, 'i', 0);
  }

  public function testGet(): void {
    $scope = new Scope();
    $scope->definePHP(false, 'i', 0);
    $value = $scope->get('i');
    $type = $scope->getType('i');
    $this->assertInstanceOf(IntegerValue::class, $value);
    $this->assertInstanceOf(IntegerType::class, $type);
  }

  public function testParentGet(): void {
    $parentScope = new Scope();
    $parentScope->definePHP(false, 'i', 0);

    $childScope = $parentScope->buildChild();

    $value = $childScope->get('i');
    $type = $childScope->getType('i');
    $this->assertInstanceOf(IntegerValue::class, $value);
    $this->assertInstanceOf(IntegerType::class, $type);
  }

  public function testGetNotDefined(): void {
    $scope = new Scope();
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('i is not defined');
    $scope->get('i');
  }

  public function testGetTypeNotDefined(): void {
    $scope = new Scope();
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('i is not defined');
    $scope->getType('i');
  }

  public function phpVarProvider(): array {
    // @formatter:off
    return [
      [1, new IntegerType(), new IntegerValue(1)],
      [1.5, new FloatType(), new FloatValue(1.5)],
      [false, new BooleanType(), new BooleanValue(false)],
      [true, new BooleanType(), new BooleanValue(true)],
      ['abc', new StringType(), new StringValue('abc')],
      [null, new NullType(), new NullValue()],
      [
        ['string' => 1, 2 => false],
        new ArrayType(CompoundType::buildFromTypes([new IntegerType(), new StringType()]), CompoundType::buildFromTypes([new IntegerType(), new BooleanType()])),
        new ArrayValue(['string' => new IntegerValue(1), 2 => new BooleanValue(false)])
      ],
    ];
    // @formatter:on
  }

  /**
   * @dataProvider phpVarProvider
   */
  public function testConvertPHPVar(mixed $phpValue, Type $expectedType, Value $expectedValue): void {
    $res = Scope::convertPHPVar($phpValue);
    $this->assertTrue($expectedType->equals($res[0]));
    $this->assertInstanceOf($expectedValue::class, $res[1]);
    $this->assertEquals($expectedValue->toPHPValue(), $res[1]->toPHPValue());
  }

  public function testConvertPHPObject(): void {
    $object = new PHPTestClass(1, 2, [1,2]);
    $res = Scope::convertPHPVar($object);
    // @formatter:off
    $expectedClassType = new ClassType(
        new ClassType(null, ParentClass::class, [
          'i' => new FieldType(false, new IntegerType()),
        ]),
        PHPTestClass::class,
        [
          'publicReadonlyInt' => new FieldType(true, new IntegerType()),
          'publicArray' => new FieldType(false, new ArrayType(new MixedType(),new MixedType())),
          'add' => new FieldType(true, new FunctionType(new OuterFunctionArgumentListType([new OuterFunctionArgument(new IntegerType(), false), new OuterFunctionArgument(new IntegerType(), false)], false), new IntegerType()))
        ]
      );
    // @formatter:on
    $this->assertTrue($expectedClassType->equals($res[0]));
    $value = $res[1];
    assertInstanceOf(PHPClassInstanceValue::class, $value);
    $this->assertTrue($value->toPHPValue() === $object);
  }

  public function testPHPObject(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'phpObject', new PHPTestClass(1, 2, [1,2]));
    $formula = new Formula('phpObject.publicReadonlyInt', $scope);
    $this->assertEquals(1, $formula->calculate()->toPHPValue());
    $formula = new Formula('phpObject.publicArray', $scope);
    $this->assertEquals([1,2], $formula->calculate()->toPHPValue());
    $formula = new Formula('phpObject.add(1,2)', $scope);
    $this->assertEquals(3, $formula->calculate()->toPHPValue());
    $formula = new Formula('phpObject.i', $scope);
    $this->assertEquals(0, $formula->calculate()->toPHPValue());
  }

  public function testFormulInFormula(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'Formula', Formula::class);
    $scope->definePHP(true, 'Scope', Scope::class);
    $formula = new Formula('
      var scope = new Scope();
      scope.definePHP(true, "i", 5);
      var formula = new Formula("i+1", scope);
      return formula.calculate();
    ', $scope);
    $this->assertEquals(6, $formula->calculate()->toPHPValue());
  }

  public function testAssignSelf(): void {
    $scope = new Scope();
    $scope->definePHP(false, 'i', 0);
    $value = $scope->get('i');
    $this->assertEquals(0, $value->toPHPValue());
    $scope->assign('i', new IntegerValue(1));
    $value = $scope->get('i');
    $this->assertEquals(1, $value->toPHPValue());
  }

  public function testAssignParent(): void {
    $parentScope = new Scope();
    $parentScope->definePHP(false, 'i', 0);
    $childScope = $parentScope->buildChild();
    $value = $childScope->get('i');
    $this->assertEquals(0, $value->toPHPValue());
    $childScope->assignPHP('i', 1);
    $value = $childScope->get('i');
    $this->assertEquals(1, $value->toPHPValue());
  }

  public function testAssignUndefined(): void {
    $scope = new Scope();
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('i is not defined');
    $scope->assignPHP('i', 0);
  }

  public function testFinal(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'i', 0);
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Cant mutate immutable value');
    $scope->assignPHP('i', 2);
  }

  public function testReadUninitilized(): void {
    $scope = new Scope();
    $scope->define(true, new IntegerType(), 'i');
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Value has been read before initilization');
    $scope->get('i');
  }

  public function testToNodeTreeScope(): void {
    $parentScope = new Scope();
    $parentScope->definePHP(false, 'i', 0);
    $childScope = $parentScope->buildChild();
    $node = $childScope->toNodeTreeScope();

    $parentNode = new NodeTreeScope(null, ['i' => (new IntegerType())->buildNodeInterfaceType()]);

    $expectedNode = new NodeTreeScope($parentNode, []);

    $this->assertEquals($expectedNode, $node);
  }
}

class ParentClass {

  public int $i = 0;
}

class PHPTestClass extends ParentClass {

  public readonly int $publicReadonlyInt;

  private int $privateInt;

  public array $publicArray;

  public function __construct(int $publicReadonlyInt, int $privateInt, array $publicArray) {
    $this->publicReadonlyInt = $publicReadonlyInt;
    $this->privateInt = $privateInt;
    $this->publicArray = $publicArray;
  }

  public function add(int $a, int $b): int {
    return $a + $b;
  }

  private function privateFunc(int $a, int $b): int {
    return $a + $b;
  }
}