<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\expression\OperatorExpression;
use TimoLehnertz\formula\expression\ConstantExpression;
use TimoLehnertz\formula\operator\SimpleOperator;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\BracketExpression;
use TimoLehnertz\formula\operator\Operator;
use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\operator\CallOperator;

class ExpressionParserTest extends TestCase {

  public function testSimpleAddition(): void {
    $firstToken = Tokenizer::tokenize("1+2");
    $result = (new ExpressionParser())->parse($firstToken);
    $this->assertNull($result->nextToken);
    $this->assertInstanceOf(OperatorExpression::class, $result->parsed);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->leftExpression);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->rightExpression);
    $this->assertInstanceOf(SimpleOperator::class, $result->parsed->operator);
    $this->assertEquals('1+2', $result->parsed->toString(PrettyPrintOptions::buildDefault()));
  }

  public function testDotBeforeDash(): void {
    $firstToken = Tokenizer::tokenize("1+2*3");
    $result = (new ExpressionParser())->parse($firstToken);
    $this->assertNull($result->nextToken);
    $this->assertInstanceOf(OperatorExpression::class, $result->parsed);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->leftExpression);
    $this->assertInstanceOf(SimpleOperator::class, $result->parsed->operator);
    $this->assertEquals('+', $result->parsed->operator->toString(PrettyPrintOptions::buildDefault()));

    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->rightExpression->leftExpression);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->rightExpression->rightExpression);
    $this->assertInstanceOf(SimpleOperator::class, $result->parsed->rightExpression->operator);
    $this->assertEquals('*', $result->parsed->rightExpression->operator->toString(PrettyPrintOptions::buildDefault()));
    $this->assertEquals('1+2*3', $result->parsed->toString(PrettyPrintOptions::buildDefault()));
  }

  public function testBracketsFirst(): void {
    $firstToken = Tokenizer::tokenize("(1+2)*3");
    $result = (new ExpressionParser())->parse($firstToken);
    $this->assertNull($result->nextToken);
    $this->assertInstanceOf(OperatorExpression::class, $result->parsed);
    $this->assertInstanceOf(BracketExpression::class, $result->parsed->leftExpression);
    $this->assertInstanceOf(SimpleOperator::class, $result->parsed->operator);
    $this->assertEquals('*', $result->parsed->operator->toString(PrettyPrintOptions::buildDefault()));
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->rightExpression);
    $this->assertEquals('(1+2)*3', $result->parsed->toString(PrettyPrintOptions::buildDefault()));
  }

  public function testUnary(): void {
    $firstToken = Tokenizer::tokenize("1+-1");
    $result = (new ExpressionParser())->parse($firstToken);
    $this->assertNull($result->nextToken);
    $this->assertInstanceOf(OperatorExpression::class, $result->parsed);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->leftExpression);
    $this->assertInstanceOf(SimpleOperator::class, $result->parsed->operator);
    $this->assertEquals('+', $result->parsed->operator->toString(PrettyPrintOptions::buildDefault()));

    $this->assertInstanceOf(OperatorExpression::class, $result->parsed->rightExpression);
    $this->assertEquals(Operator::TYPE_UNARY_MINUS, $result->parsed->rightExpression->operator->id);
    $this->assertNull($result->parsed->rightExpression->leftExpression);
    $this->assertInstanceOf(ConstantExpression::class, $result->parsed->rightExpression->rightExpression);
    $this->assertEquals('1+-1', $result->parsed->toString(PrettyPrintOptions::buildDefault()));
  }

  public function testCallOperator(): void {
    $firstToken = Tokenizer::tokenize("a(1)");
    $result = (new ExpressionParser())->parse($firstToken);
    $this->assertNull($result->nextToken);
    $this->assertInstanceOf(OperatorExpression::class, $result->parsed);
    $this->assertInstanceOf(IdentifierExpression::class, $result->parsed->leftExpression);
    $this->assertInstanceOf(CallOperator::class, $result->parsed->operator);
    $this->assertNull($result->parsed->rightExpression);

    $this->assertEquals('a(1)', $result->parsed->toString(PrettyPrintOptions::buildDefault()));
  }
}
