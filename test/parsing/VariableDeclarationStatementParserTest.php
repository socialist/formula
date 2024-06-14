<?php
namespace test\parsing;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\parsing\VariableDeclarationStatementParser;
use TimoLehnertz\formula\statement\VariableDeclarationStatement;
use TimoLehnertz\formula\tokens\Tokenizer;

class VariableDeclarationStatementParserTest extends TestCase {

  public function testIntAInitilizer(): void {
    $firstToken = Tokenizer::tokenize("int a = 0;");
    $type = (new VariableDeclarationStatementParser())->parse($firstToken);
    $this->assertNull($type->nextToken);
    $this->assertInstanceOf(VariableDeclarationStatement::class, $type->parsed);
  }
}
