<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\ReturnStatementParser;
use TimoLehnertz\formula\statement\ReturnStatement;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\expression\ConstantExpression;

class ReturnStatementParserTest extends TestCase {

  public function testOnlyReturn(): void {
    $firstToken = Tokenizer::tokenize("return;");
    $parsedStatement = (new ReturnStatementParser())->parse($firstToken);
    $this->assertNull($parsedStatement->nextToken);
    $this->assertInstanceOf(ReturnStatement::class, $parsedStatement->parsed);
    $this->assertNull($parsedStatement->parsed->getExpression());
  }

  public function testInitilizer(): void {
    $firstToken = Tokenizer::tokenize("return 1;");
    $parsedStatement = (new ReturnStatementParser())->parse($firstToken);
    $this->assertNull($parsedStatement->nextToken);
    $this->assertInstanceOf(ReturnStatement::class, $parsedStatement->parsed);
    $this->assertInstanceOf(ConstantExpression::class, $parsedStatement->parsed->getExpression());
  }
}
