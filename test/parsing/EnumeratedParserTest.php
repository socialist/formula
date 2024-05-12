<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\EnumeratedParser;
use TimoLehnertz\formula\parsing\TypeParser;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\ParsingException;

class EnumeratedParserTest extends TestCase {

  public function testOK(): void {
    $firstToken = Tokenizer::tokenize("{float,string,int}");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertIsArray($parsed->parsed);
    $this->assertCount(3, $parsed->parsed);
    $this->assertInstanceOf(FloatType::class, $parsed->parsed[0]);
    $this->assertInstanceOf(StringType::class, $parsed->parsed[1]);
    $this->assertInstanceOf(IntegerType::class, $parsed->parsed[2]);
  }

  public function testInvalidStart(): void {
    $firstToken = Tokenizer::tokenize("int");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertEquals(ParsingException::PARSING_ERROR_GENERIC, $parsed);
  }

  public function testInvalidLastDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,}");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertEquals(ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $parsed);
  }

  public function testGoodLastDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,}");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, true);
    $parsed = $parser->parse($firstToken);
    $this->assertIsArray($parsed->parsed);
    $this->assertCount(1, $parsed->parsed);
    $this->assertInstanceOf(IntegerType::class, $parsed->parsed[0]);
  }

  public function testBadInBetweenDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int,,int}");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parse($firstToken);
    $this->assertEquals(ParsingException::PARSING_ERROR_TOO_MANY_DELIMITERS, $parsed);
  }

  public function testWrongDelimiter(): void {
    $firstToken = Tokenizer::tokenize("{int;}");
    $parser = new EnumeratedParser(new TypeParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, true, false);
    $parsed = $parser->parse($firstToken);
    $this->assertEquals(ParsingException::PARSING_ERROR_MISSING_DELIMITERS, $parsed);
  }
}
