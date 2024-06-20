<?php
namespace test\tokens;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\tokens\Token;

class TokenTest extends TestCase {

  public function testPrev(): void {
    $tokenA = new Token(Token::IDENTIFIER, 'a', 0, 0, '', null);
    $this->assertFalse($tokenA->hasPrev());
    $this->assertNull($tokenA->prev());
    $this->assertFalse($tokenA->hasNext());
    $this->assertNull($tokenA->next());

    $tokenPlus = new Token(Token::PLUS, '+', 0, 0, '', $tokenA);
    $this->assertTrue($tokenPlus->hasPrev());
    $this->assertEquals($tokenA, $tokenPlus->prev());
    $this->assertTrue($tokenA->hasNext());
    $this->assertEquals($tokenPlus, $tokenA->next());
    $this->assertFalse($tokenA->hasPrev());
    $this->assertNull($tokenA->prev());

    $tokenComment = new Token(Token::LINE_COMMENT, '// b incoming', 0, 0, '', $tokenPlus);
    $this->assertFalse($tokenPlus->hasNext());
    $this->assertTrue($tokenPlus->hasNext(true));
    $this->assertEquals($tokenComment, $tokenPlus->next(true));

    $tokenB = new Token(Token::IDENTIFIER, 'b', 0, 0, '', $tokenComment);
    $this->assertTrue($tokenPlus->hasNext());
    $this->assertEquals($tokenB, $tokenPlus->next());
    $this->assertEquals($tokenComment, $tokenPlus->next(true));
    $this->assertEquals($tokenComment, $tokenB->prev(true));
    $this->assertEquals($tokenPlus, $tokenB->prev());
  }
}
