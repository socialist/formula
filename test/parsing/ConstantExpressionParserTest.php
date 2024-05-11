<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\parsing\ConstantExpressionParser;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\NullValue;

class ConstantExpressionParserTest extends TestCase {

  public function testFloat(): void {
    $firstToken = Tokenizer::tokenize("123.456");
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertEquals(123.456, $parsedConstant->parsed->run()->getValue());
  }

  public function testInt(): void {
    $firstToken = Tokenizer::tokenize("123");
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertEquals(123, $parsedConstant->parsed->run()->getValue());
  }

  public function testFalse(): void {
    $firstToken = Tokenizer::tokenize("false");
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertEquals(false, $parsedConstant->parsed->run()->getValue());
  }

  public function testTrue(): void {
    $firstToken = Tokenizer::tokenize("true");
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertEquals(true, $parsedConstant->parsed->run()->getValue());
  }

  public function testString(): void {
    $firstToken = Tokenizer::tokenize("'abc'");
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertEquals('abc', $parsedConstant->parsed->run()->getValue());
  }

  public function testNull(): void {
    $firstToken = Tokenizer::tokenize('null');
    $parsedConstant = (new ConstantExpressionParser())->parse($firstToken);
    $this->assertNull($parsedConstant->nextToken);
    $this->assertInstanceOf(ConstantExpression::class, $parsedConstant->parsed);
    $this->assertInstanceOf(NullValue::class, $parsedConstant->parsed->run());
  }
}
