<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\ArrayOperatorParser;
use TimoLehnertz\formula\parsing\EnumeratedParser;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\tokens\Tokenizer;

class EnumeratedParserTest extends TestCase {

  public function testOK(): void {
    $firstToken = Tokenizer::tokenize("{[],[],[]}");
    $parser = new EnumeratedParser(new ArrayOperatorParser(), Token::CURLY_BRACKETS_OPEN, Token::COMMA, Token::CURLY_BRACKETS_CLOSED, false, false);
    $parsed = $parser->parseRequired($firstToken);
    var_dump($parsed);
  }
}

