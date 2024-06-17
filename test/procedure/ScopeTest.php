<?php
namespace test\procedure;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\FormulaRuntimeException;
use TimoLehnertz\formula\nodes\NodeTreeScope;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringValue;

function my_callback_function(): int {
  return 1;
}
class ScopeTest extends TestCase {

  public function testDefine(): void {
    $scope = new Scope();
    $this->assertFalse($scope->isDefined('i'));
    $scope->define(false, new IntegerType(), 'i', new IntegerValue(0));
    $this->assertTrue($scope->isDefined('i'));
  }

  public function testRedefine(): void {
    $scope = new Scope();
    $scope->define(false, new IntegerType(), 'i', new IntegerValue(0));
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Can\'t redefine i');
    $scope->define(false, new IntegerType(), 'i', new IntegerValue(0));
  }

  public function testGet(): void {
    $scope = new Scope();
    $scope->define(false, new IntegerType(), 'i', new IntegerValue(0));
    $value = $scope->get('i');
    $type = $scope->getType('i');
    $this->assertInstanceOf(IntegerValue::class, $value);
    $this->assertInstanceOf(IntegerType::class, $type);
  }

  public function testParentGet(): void {
    $parentScope = new Scope();
    $parentScope->define(false, new IntegerType(), 'i', new IntegerValue(0));

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

  public function testValueByPHPVar(): void {
    $this->assertInstanceOf(IntegerValue::class, Scope::valueByPHPVar(1));
    $this->assertInstanceOf(FloatValue::class, Scope::valueByPHPVar(1.0));
    $this->assertInstanceOf(BooleanValue::class, Scope::valueByPHPVar(false));
    $this->assertInstanceOf(StringValue::class, Scope::valueByPHPVar('string'));
    $this->assertInstanceOf(NullValue::class, Scope::valueByPHPVar(null));
    $this->assertInstanceOf(ArrayValue::class, Scope::valueByPHPVar([null]));
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Unsupported php type');
    $this->assertInstanceOf(ArrayValue::class, Scope::valueByPHPVar(new Scope()));
  }

  public function testAssignSelf(): void {
    $scope = new Scope();
    $scope->define(false, new IntegerType(), 'i', new IntegerValue(0));
    $value = $scope->get('i');
    $this->assertEquals(0, $value->toPHPValue());
    $scope->assign('i', new IntegerValue(1));
    $value = $scope->get('i');
    $this->assertEquals(1, $value->toPHPValue());
  }

  public function testAssignParent(): void {
    $parentScope = new Scope();
    $parentScope->define(false, new IntegerType(), 'i', new IntegerValue(0));
    $childScope = $parentScope->buildChild();
    $value = $childScope->get('i');
    $this->assertEquals(0, $value->toPHPValue());
    $childScope->assign('i', 1);
    $value = $childScope->get('i');
    $this->assertEquals(1, $value->toPHPValue());
  }

  public function testAssignUndefined(): void {
    $scope = new Scope();
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('i is not defined');
    $scope->assign('i', 0);
  }

  public function testFinal(): void {
    $scope = new Scope();
    $scope->define(true, new IntegerType(), 'i', 0);
    $this->expectException(FormulaRuntimeException::class);
    $this->expectExceptionMessage('Cant mutate immutable value');
    $scope->assign('i', 2);
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
    $parentScope->define(false, new IntegerType(), 'i', 0);
    $childScope = $parentScope->buildChild();
    $node = $childScope->toNodeTreeScope();

    $parentNode = new NodeTreeScope(null, ['i' => (new IntegerType())->buildNodeInterfaceType()]);

    $expectedNode = new NodeTreeScope($parentNode, []);

    $this->assertEquals($expectedNode, $node);
  }
}
