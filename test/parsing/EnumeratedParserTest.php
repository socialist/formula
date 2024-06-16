<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\expression\IdentifierExpression;
use TimoLehnertz\formula\parsing\EnumeratedParser;
use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\parsing\ParsingException;
use TimoLehnertz\formula\parsing\TypeParser;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\parsing\ParsingSkippedException;

class EnumeratedParserTest extends TestCase {

  public function testOK(): void {
    $firstToken = Tokenizer::tokenize("{float,String,int}");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertIsArray($parsed->parsed);
    $this->assertCount(3, $parsed->parsed);
    $this->assertInstanceOf(FloatType::class, $parsed->parsed[0]);
    $this->assertInstanceOf(StringType::class, $parsed->parsed[1]);
    $this->assertInstanceOf(IntegerType::class, $parsed->parsed[2]);
  }

  public function testInvalidStart(): void {
    $firstToken = Tokenizer::tokenize("int");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $this->expectException(ParsingSkippedException::class);
    $parser->parse($firstToken);
  }

  public function testInvalidLastDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,}");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage((new ParsingException($parser, ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $firstToken->next()->next()->next()))->getMessage());
    $parser->parse($firstToken);
  }

  public function testGoodLastDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,}");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, true);
    $parsed = $parser->parse($firstToken);
    $this->assertIsArray($parsed->parsed);
    $this->assertCount(1, $parsed->parsed);
    $this->assertInstanceOf(IntegerType::class, $parsed->parsed[0]);
  }

  public function testBadInBetweenDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,,int}");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage((new ParsingException($parser, ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $firstToken->next()->next()->next()))->getMessage());
    $parser->parse($firstToken);
  }

  public function testWrongDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int;}");
    $parser = new EnumeratedParser('', new TypeParser(false), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, true, false);
    $this->expectException(ParsingException::class);
    $this->expectExceptionMessage((new ParsingException($parser, ParsingException::PARSING_ERROR_MISSING_DELIMITERS, $firstToken->next()->next()))->getMessage());
    $parser->parse($firstToken);
  }

  public function testExpression(): void {
    $firstToken = Tokenizer::tokenize("(a,b,c)");
    $parser = new EnumeratedParser('', new ExpressionParser(), Token::BRACKETS_OPEN, Token::COMMA, Token::BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertIsArray($parsed->parsed);
    $this->assertCount(3, $parsed->parsed);
    $this->assertInstanceOf(IdentifierExpression::class, $parsed->parsed[0]);
    $this->assertInstanceOf(IdentifierExpression::class, $parsed->parsed[1]);
    $this->assertInstanceOf(IdentifierExpression::class, $parsed->parsed[2]);
  }
}
