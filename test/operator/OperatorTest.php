<?php
namespace TimoLehnertz\formula\operator;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\ExpressionParser;
use TimoLehnertz\formula\tokens\Tokenizer;

class OperatorTest extends TestCase {

  public function testImplicitCast(): void {
    $firstToken = Tokenizer::tokenize("123.123 = 123");
    $expression = (new ExpressionParser())->parse($firstToken);
  }
}
