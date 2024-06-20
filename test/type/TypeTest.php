<?php
namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\FormulaBugException;
use TimoLehnertz\formula\operator\ImplementableOperator;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\ArrayValue;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\DateIntervalType;
use TimoLehnertz\formula\type\DateIntervalValue;
use TimoLehnertz\formula\type\DateTimeImmutableType;
use TimoLehnertz\formula\type\DateTimeImmutableValue;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\MixedType;
use TimoLehnertz\formula\type\NeverType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeType;
use TimoLehnertz\formula\type\TypeValue;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use TimoLehnertz\formula\type\classes\ClassInstanceValue;
use TimoLehnertz\formula\type\classes\ClassType;
use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\FunctionValue;
use TimoLehnertz\formula\type\functions\OuterFunctionArgument;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListValue;
use TimoLehnertz\formula\type\functions\PHPFunctionBody;
use const false;
use const true;
use TimoLehnertz\formula\type\NullType;
use TimoLehnertz\formula\type\NullValue;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\MemberAccsessType;
use TimoLehnertz\formula\type\MemberAccsessValue;
use TimoLehnertz\formula\type\classes\FieldType;
use TimoLehnertz\formula\type\classes\FieldValue;
use TimoLehnertz\formula\type\classes\ClassTypeType;
use TimoLehnertz\formula\type\classes\ConstructorType;
use TimoLehnertz\formula\type\classes\ClassTypeValue;
use TimoLehnertz\formula\type\classes\ConstructorValue;

class TypeTest extends TestCase {

  public function provider(): array {
    $tests = [];
    /**
     * IntegerType
     */
    $operators = [];
    // Operator +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new IntegerType(), new IntegerValue(8), new IntegerValue(50));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(8), new FloatValue(50));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_ADDITION, $compatibleOperands);
    // Operator -
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new IntegerType(), new IntegerValue(2), new IntegerValue(40));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(40));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_SUBTRACTION, $compatibleOperands);
    // Operator *
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new IntegerType(), new IntegerValue(2), new IntegerValue(84));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(84));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_MULTIPLICATION, $compatibleOperands);
    // Operator /
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(2), new FloatValue(21));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(21));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_DIVISION, $compatibleOperands);
    // Operator <
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new BooleanType(), new IntegerValue(2), new BooleanValue(false));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new BooleanType(), new FloatValue(2), new BooleanValue(false));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_LESS, $compatibleOperands);
    // Operator >
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new BooleanType(), new IntegerValue(2), new BooleanValue(true));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new BooleanType(), new FloatValue(2), new BooleanValue(true));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_GREATER, $compatibleOperands);
    // Operator %
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new IntegerType(), new IntegerValue(2), new IntegerValue(0));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_MODULO, $compatibleOperands);
    // Operator (float)
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new TypeType(new FloatType()), new FloatType(), new TypeValue(new FloatType()), new FloatValue(42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_TYPE_CAST, $compatibleOperands);
    // Operator unary +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, new IntegerType(), null, new IntegerValue(42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_UNARY_PLUS, $compatibleOperands);
    // Operator unary -
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, new IntegerType(), null, new IntegerValue(-42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_UNARY_MINUS, $compatibleOperands);
    $tests[] = [new IntegerType(),new IntegerType(),new FloatType(),new IntegerType(),new FloatType(),'int',$operators,new IntegerValue(42),'42',true,new IntegerValue(42),new IntegerValue(1),true];

    /**
     * FloatType
     */
    $operators = [];
    // Operator +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(8), new FloatValue(50));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(8), new FloatValue(50));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_ADDITION, $compatibleOperands);
    // Operator -
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(2), new FloatValue(40));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(40));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_SUBTRACTION, $compatibleOperands);
    // Operator *
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(2), new FloatValue(84));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(84));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_MULTIPLICATION, $compatibleOperands);
    // Operator /
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(2), new FloatValue(21));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new FloatType(), new FloatValue(2), new FloatValue(21));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_DIVISION, $compatibleOperands);
    // Operator <
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new BooleanType(), new IntegerValue(2), new BooleanValue(false));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new BooleanType(), new FloatValue(2), new BooleanValue(false));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_LESS, $compatibleOperands);
    // Operator >
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new BooleanType(), new IntegerValue(2), new BooleanValue(true));
    $compatibleOperands[] = new CompatibleOperator(new FloatType(), new BooleanType(), new FloatValue(2), new BooleanValue(true));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_GREATER, $compatibleOperands);
    // Operator (int)
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new TypeType(new IntegerType()), new IntegerType(), new TypeValue(new IntegerType()), new IntegerValue(42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_TYPE_CAST, $compatibleOperands);
    // Operator unary +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, new FloatType(), null, new FloatValue(42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_UNARY_PLUS, $compatibleOperands);
    // Operator unary -
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, new FloatType(), null, new FloatValue(-42));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_UNARY_MINUS, $compatibleOperands);
    $tests[] = [new FloatType(),new FloatType(),new IntegerType(),new FloatType(),new IntegerType(),'float',$operators,new FloatValue(42),'42.0',true,new FloatValue(42),new FloatValue(1),true];

    /**
     * ArrayType
     */
    $operators = [];
    // Operator []
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new IntegerType(), new FloatType(), new IntegerValue(1), new FloatValue(1));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_ARRAY_ACCESS, $compatibleOperands);

    $equal = new ArrayType(new IntegerType(), new FloatType());
    $notEqual = new ArrayType(new IntegerType(), new IntegerType());
    $assignable = new ArrayType(new IntegerType(), new FloatType());
    $notAssignable = new ArrayType(new IntegerType(), new IntegerType());

    $arrayValue = new ArrayValue([new FloatValue(0),new FloatValue(1)]);
    $tests[] = [new ArrayType(new IntegerType(), new FloatType()),$equal,$notEqual,$assignable,$notAssignable,'float[]',$operators,$arrayValue,'{0.0,1.0}',true,$arrayValue,new ArrayValue([]),false];
    $tests[] = [new ArrayType(new StringType(), new FloatType()),new ArrayType(new StringType(), new FloatType()),$notEqual,new ArrayType(new StringType(), new FloatType()),$notAssignable,'array<String,float>',[],$arrayValue,'{0.0,1.0}',true,$arrayValue,new ArrayValue([]),false]; // test getIdentifier

    /**
     * BooleanType
     */
    $operators = [];
    // Operator !
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, new BooleanType(), null, new BooleanValue(false));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_LOGICAL_NOT, $compatibleOperands);
    // Operator &&
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new BooleanType(), new BooleanType(), new BooleanValue(false), new BooleanValue(false));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_LOGICAL_AND, $compatibleOperands);

    $tests[] = [new BooleanType(),new BooleanType(),new IntegerType(),new BooleanType(),new IntegerType(),'boolean',$operators,new BooleanValue(true),'true',true,new BooleanValue(true),new BooleanValue(false),true];

    /**
     * DateIntervalType
     */
    $tests[] = [new DateIntervalType(),new DateIntervalType(),new IntegerType(),new DateIntervalType(),new IntegerType(),'DateInterval',[],new DateIntervalValue(new \DateInterval('P1Y1M1DT1H1M1S')),'P1Y1M1DT1H1M1S',true,new DateIntervalValue(new \DateInterval('P1Y1M1DT1H1M1S')),new DateIntervalValue(new \DateInterval('P0D')),true];

    /**
     * OuterFunctionArgumentListType
     */
    $tests[] = [new OuterFunctionArgumentListType([], false),new OuterFunctionArgumentListType([], false),new IntegerType(),new OuterFunctionArgumentListType([], false),new OuterFunctionArgumentListType([new OuterFunctionArgument(new VoidType(), false, false)], false),'()',[],new OuterFunctionArgumentListValue([]),'OuterFunctionArgumentListValue',true,null,new OuterFunctionArgumentListValue([]),false];

    /**
     * DateTimeImmutableType
     */
    $operators = [];
    // Operator +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new DateIntervalType(), new DateTimeImmutableType(), new DateIntervalValue(new \DateInterval('P1D')), new DateTimeImmutableValue(new \DateTimeImmutable('2024-01-06')));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_ADDITION, $compatibleOperands);
    // Operator -
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new DateIntervalType(), new DateTimeImmutableType(), new DateIntervalValue(new \DateInterval('P1D')), new DateTimeImmutableValue(new \DateTimeImmutable('2024-01-04')));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_SUBTRACTION, $compatibleOperands);

    $tests[] = [new DateTimeImmutableType(),new DateTimeImmutableType(),new IntegerType(),new DateTimeImmutableType(),new IntegerType(),'DateTimeImmutable',$operators,new DateTimeImmutableValue(new \DateTimeImmutable('2024-01-05')),'2024-01-05T00:00:00',true,new DateTimeImmutableValue(new \DateTimeImmutable('2024-01-05')),new DateTimeImmutableValue(new \DateTimeImmutable('2024-01-04')),true];

    /**
     * FunctionType
     */
    $operators = [];
    // Operator ()
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new OuterFunctionArgumentListType([], false), new VoidType(), new OuterFunctionArgumentListValue([]), new VoidValue());
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_CALL, $compatibleOperands);

    $args = new OuterFunctionArgumentListType([], false);
    $functionValue = new FunctionValue(new PHPFunctionBody(function () {
      return 0;
    }, true));
    $functionValue2 = new FunctionValue(new PHPFunctionBody(function () {
      return 0;
    }, true));
    $tests[] = [new FunctionType($args, new VoidType()),new FunctionType($args, new VoidType()),new DateTimeImmutableType(),new FunctionType($args, new VoidType()),new FunctionType($args, new IntegerType()),'function() -> void',$operators,$functionValue,'function',true,$functionValue,$functionValue2,true];

    /**
     * MixedType
     */
    $tests[] = [new MixedType(),new MixedType(),new IntegerType(),new MixedType(),new IntegerType(),'mixed',[],null,null,null,null,null,null];

    /**
     * NeverType
     */
    $tests[] = [new NeverType(),new NeverType(),new IntegerType(),new NeverType(),new IntegerType(),'never',[],null,null,null,null,null,null];

    /**
     * NullType
     */
    $tests[] = [new NullType(),new NullType(),new IntegerType(),new NullType(),new IntegerType(),'null',[],new NullValue(),'null',false,new NullValue(),new IntegerValue(1),true];

    /**
     * TypeType
     */
    $tests[] = [new TypeType(new IntegerType()),new TypeType(new IntegerType()),new IntegerType(),new TypeType(new IntegerType()),new TypeType(new BooleanType()),'TypeType(int)',[],new TypeValue(new IntegerType()),'TypeValue(int)',true,new TypeValue(new IntegerType()),new TypeValue(new BooleanType()),true];

    /**
     * VoidType
     */
    $tests[] = [new VoidType(),new VoidType(),new IntegerType(),new VoidType(),new IntegerType(),'void',[],new VoidValue(),'void',false,new VoidValue(),new IntegerValue(1),true];

    /**
     * MemberAccsessType
     */
    $tests[] = [new MemberAccsessType('a'),new MemberAccsessType('a'),new IntegerType(),new MemberAccsessType('a'),new IntegerType(),'member accsess(a)',[],new MemberAccsessValue('a'),'a',true,new MemberAccsessValue('a'),new MemberAccsessValue('b'),true];

    /**
     * StringType
     */
    $operators = [];
    // Operator +
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new StringType(), new StringType(), new StringValue(' world!'), new StringValue('Hello world!'));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_ADDITION, $compatibleOperands);

    $tests[] = [new StringType(),new StringType(),new IntegerType(),new StringType(),new IntegerType(),'String',$operators,new StringValue('Hello'),'Hello',true,new StringValue('Hello'),new StringValue('no equal'),true];

    /**
     * ClassType
     */
    $operators = [];
    // Operator .
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(new MemberAccsessType('f'), new IntegerType(), new MemberAccsessValue('f'), new IntegerValue(1));
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_MEMBER_ACCESS, $compatibleOperands);

    $classType = new ClassType(null, 'C', ['f' => new FieldType(false, new IntegerType())]);
    $tests[] = [$classType,$classType,new IntegerType(),$classType,new IntegerType(),'classType(C)',$operators,new ClassInstanceValue(['f' => new FieldValue(new IntegerValue(1))]),'classInstance',true,null,new IntegerValue(1),false];

    /**
     * ClassTypeType
     */
    $classTypeType = new ClassTypeType(new ConstructorType(new OuterFunctionArgumentListType([], false), $classType));
    $constructor = new ConstructorValue(new PHPFunctionBody(function () {
      return new IntegerValue(1);
    }, false));
    $constructorType = new ConstructorType(new OuterFunctionArgumentListType([], false), $classType);
    $operators = [];
    // Operator new
    $compatibleOperands = [];
    $compatibleOperands[] = new CompatibleOperator(null, $constructorType, null, $constructor);
    $operators[] = new OperatorTestMeta(ImplementableOperator::TYPE_NEW, $compatibleOperands);

    $tests[] = [$classTypeType,$classTypeType,new IntegerType(),$classTypeType,new IntegerType(),'ClassTypeType',$operators,new ClassTypeValue($constructor),'classType',true,null,new IntegerValue(1),false];

    return $tests;
  }

  /**
   * @dataProvider provider
   */
  public function testTypes(Type $type, Type $equal, Type $notEqual, Type $assignable, Type $notAssignable, string $expectedIdentifier, array $operators, ?Value $testValue, ?string $expectedValueString, ?bool $expectedTruthyness, ?Value $equalValue, ?Value $notEqualValue, ?bool $copyEquals): void {
    $this->assertTrue($type->equals($equal));
    $this->assertFalse($type->equals($notEqual));
    $this->assertTrue($type->assignableBy($assignable));
    $this->assertFalse($type->equals($notAssignable));
    $this->assertEquals($expectedIdentifier, $type->getIdentifier());

    if($testValue !== null) {
      $this->assertEquals($expectedValueString, $testValue->toString());
      $this->assertEquals($expectedTruthyness, $testValue->isTruthy());

      if($equalValue !== null) {
        $this->assertTrue($testValue->valueEquals($equalValue));
      }
      $this->assertFalse($testValue->valueEquals($notEqualValue));
      $this->assertFalse($testValue->valueEquals(new ClassInstanceValue([])));
      $this->assertEquals($copyEquals, $testValue->valueEquals($testValue->copy()));
    }

    // test compatible Operators
    foreach($operators as $operatorMeta) {
      $compatible = $type->getCompatibleOperands($operatorMeta->operator);
      $expectedCompatible = $operatorMeta->getExpectedCompatible();
      if(count($expectedCompatible) === 0) {
        $this->assertCount(0, $compatible);
      } else {
        if(!CompoundType::buildFromTypes($compatible)->equals(CompoundType::buildFromTypes($expectedCompatible))) {
          var_dump($compatible, $expectedCompatible);
        }
        $this->assertTrue(CompoundType::buildFromTypes($compatible)->equals(CompoundType::buildFromTypes($expectedCompatible)));
      }
      foreach($operatorMeta->compatibleOperands as $compatibleOperand) {
        $resultType = $type->getOperatorResultType($operatorMeta->operator, $compatibleOperand->operand);
        if($compatibleOperand->operand !== null) {
          $this->assertNull($type->getOperatorResultType($operatorMeta->operator, null)); // test missing operand
          if($operatorMeta->operator->getID() !== ImplementableOperator::TYPE_LOGICAL_AND) {
            $this->assertNull($type->getOperatorResultType($operatorMeta->operator, new ClassType(null, '---', []))); // test invalid operand
          }
        }
        if(!$compatibleOperand->resultType->equals($resultType)) {
          var_dump($compatibleOperand->resultType, $resultType);
        }
        $this->assertTrue($compatibleOperand->resultType->equals($resultType));

        if($compatibleOperand->expectedRetult !== null) {
          $resultValue = $testValue->operate($operatorMeta->operator, $compatibleOperand->testValue);
          /**
           * Test that no other exception than FormulaBugException is beeing thrown when operating an operator with missing or invalid value
           */
          if($compatibleOperand->testValue !== null) {
            try {
              $testValue->operate($operatorMeta->operator, null);
              $this->fail('Expected FormulaBugException');
            } catch(FormulaBugException $e) {
              $this->once();
            }
            //             try {
            //               $testValue->operate($operatorMeta->operator, new NullValue());
            //               $this->fail('Expected FormulaBugException');
            //             } catch(FormulaBugException $e) {
            //               $this->once();
            //             }
          }
          /**
           * Check that the resulting value is correct
           */
          if(!$compatibleOperand->expectedRetult->valueEquals($resultValue)) {
            var_dump($compatibleOperand->expectedRetult, $resultValue);
          }
          $this->assertTrue($compatibleOperand->expectedRetult->valueEquals($resultValue));
        }
      }
    }
    // test incompatible
    $this->assertEquals([], $type->getCompatibleOperands(new ImplementableOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION)));
    $this->assertNull($type->getOperatorResultType(new ImplementableOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION), null));
    $this->assertNull($type->getOperatorResultType(new ImplementableOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION), new IntegerType()));
    if($testValue !== null) {
      try {
        $testValue->operate(new ImplementableOperator(ImplementableOperator::TYPE_SCOPE_RESOLUTION), null);
        $this->fail('Expected FormulaBugException');
      } catch(FormulaBugException $e) {}
    }
  }
}

class OperatorTestMeta {

  public readonly ImplementableOperator $operator;

  public array $compatibleOperands;

  public function __construct(int $implementableOperatorID, array $compatibleOperands) {
    $this->operator = new ImplementableOperator($implementableOperatorID);
    $this->compatibleOperands = $compatibleOperands;
    if($implementableOperatorID === ImplementableOperator::TYPE_TYPE_CAST) {
      $this->compatibleOperands[] = new CompatibleOperator(new TypeType(new BooleanType()), new BooleanType(), null, null);
      $this->compatibleOperands[] = new CompatibleOperator(new TypeType(new StringType()), new StringType(), null, null);
    }
  }

  public function getExpectedCompatible(): array {
    $types = [];
    foreach($this->compatibleOperands as $compatibleOperand) {
      if($compatibleOperand->operand !== null) {
        $types[] = $compatibleOperand->operand;
      }
    }
    return $types;
  }
}

class CompatibleOperator {

  public readonly ?Type $operand;

  public readonly Type $resultType;

  public readonly ?Value $testValue;

  public readonly ?Value $expectedRetult;

  public function __construct(?Type $operand, Type $resultType, ?Value $testValue, ?Value $expectedRetult) {
    $this->operand = $operand;
    $this->resultType = $resultType;
    $this->testValue = $testValue;
    $this->expectedRetult = $expectedRetult;
  }
}
