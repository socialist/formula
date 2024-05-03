<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\parsing\TypeParser;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\ReferenceType;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\CompoundType;

class TypeParserTest extends TestCase {

  public function testInt(): void {
    $tokens = Formula::tokenize("int");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(IntegerType::class, $type);
  }

  public function testBool(): void {
    $tokens = Formula::tokenize("bool");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(BooleanType::class, $type);
  }

  public function testReferenceType(): void {
    $tokens = Formula::tokenize("abc");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(ReferenceType::class, $type);
    $this->assertEquals('abc', $type->getIdentifier());
  }

  public function testArray(): void {
    $tokens = Formula::tokenize("int[]");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(ArrayType::class, $type);
    $this->assertEquals('int[]', $type->getIdentifier());
  }

  public function testCompound(): void {
    $tokens = Formula::tokenize("int|bool|abc");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(CompoundType::class, $type);
    $this->assertEquals('int|bool|abc', $type->getIdentifier());
  }

  public function testCompoundArray(): void {
    $tokens = Formula::tokenize("int|bool[]|abc");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(CompoundType::class, $type);
    $this->assertEquals('int|bool[]|abc', $type->getIdentifier());
  }

  public function testNestedCompoundArray(): void {
    $tokens = Formula::tokenize("(int|bool|abc)[]");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(ArrayType::class, $type);
    $this->assertEquals('(int|bool|abc)[]', $type->getIdentifier());
  }

  public function testDeeplyNestedCompound(): void {
    $tokens = Formula::tokenize("int|(bool|(abcd|abc))[]");
    $index = 0;
    $type = TypeParser::parseType($tokens, $index);
    $this->assertInstanceOf(CompoundType::class, $type);
    $this->assertEquals('int|(bool|(abcd|abc))[]', $type->getIdentifier());
  }
}

