<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\TypeParser;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\StringType;

class TypeParserTest extends TestCase {

  public function testInt(): void {
    $firstToken = Tokenizer::tokenize("int");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(IntegerType::class, $type->parsed);
  }

  public function testBool(): void {
    $firstToken = Tokenizer::tokenize("bool");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(BooleanType::class, $type->parsed);
  }

  public function testString(): void {
    $firstToken = Tokenizer::tokenize("string");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(StringType::class, $type->parsed);
  }

  public function testArray(): void {
    $firstToken = Tokenizer::tokenize("int[]");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(ArrayType::class, $type->parsed);
    $this->assertEquals('int[]', $type->parsed->getIdentifier());
  }

  public function testCompound(): void {
    $firstToken = Tokenizer::tokenize("int|bool");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(CompoundType::class, $type->parsed);
    $this->assertEquals('int|bool', $type->parsed->getIdentifier());
  }

  public function testCompoundArray(): void {
    $firstToken = Tokenizer::tokenize("int|bool[]");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(CompoundType::class, $type->parsed);
    $this->assertEquals('int|bool[]', $type->parsed->getIdentifier());
  }

  public function testNestedCompoundArray(): void {
    $firstToken = Tokenizer::tokenize("(int|bool)[]");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(ArrayType::class, $type->parsed);
    $this->assertEquals('(int|bool)[]', $type->parsed->getIdentifier());
  }

  public function testDeeplyNestedCompound(): void {
    $firstToken = Tokenizer::tokenize("int|(bool|(int|int))[]");
    $type = (new TypeParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(CompoundType::class, $type->parsed);
    $this->assertEquals('int|(bool|int)[]', $type->parsed->getIdentifier());
  }
}
