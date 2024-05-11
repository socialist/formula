<?php
namespace test\tokens;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\tokens\Token;

class TokenizerTest extends TestCase {

  // @formatter:off
  public static function getTokens(): array {
    return [
      [Token::KEYWORD_INT, 'int ', 'int'],
      [Token::KEYWORD_FLOAT, 'float ', 'float'],
      [Token::KEYWORD_STRING, 'string ', 'string'],
      [Token::KEYWORD_BOOL, 'bool ', 'bool'],
      [Token::KEYWORD_CHAR, 'char ', 'char'],
      [Token::KEYWORD_NEW, 'new ', 'new'],
      [Token::KEYWORD_RETURN, 'return ', 'return'],
      [Token::KEYWORD_CONTINUE, 'continue ', 'continue'],
      [Token::KEYWORD_BREAK, 'break ', 'break'],
      [Token::KEYWORD_TRUE, 'true ', 'true'],
      [Token::KEYWORD_FALSE, 'false ', 'false'],
      [Token::KEYWORD_VOID, 'void ', 'void'],
      [Token::KEYWORD_NULL, 'null ', 'null'],
      [Token::KEYWORD_IF, 'if ', 'if'],
      [Token::KEYWORD_WHILE, 'while ', 'while'],
      [Token::KEYWORD_DO, 'do ', 'do'],
      [Token::KEYWORD_FOR, 'for ', 'for'],
      [Token::KEYWORD_INSTANCEOF, 'instanceof', 'instanceof'],
      [Token::COlON, ':', ':'],
      [Token::QUESTIONMARK, '?', '?'],
      [Token::LOGICAL_AND, '&&', '&&'],
      [Token::LOGICAL_OR, '||', '||'],
      [Token::LOGICAL_XOR, '^', '^'],
      [Token::EXCLAMATION_MARK, '!', '!'],
      [Token::COMPARISON_EQUALS, '==', '=='],
      [Token::COMPARISON_NOT_EQUALS, '!=', '!='],
      [Token::COMPARISON_SMALLER, '<', '<'],
      [Token::COMPARISON_SMALLER_EQUALS, '<=', '<='],
      [Token::COMPARISON_GREATER, '>', '>'],
      [Token::COMPARISON_GREATER_EQUALS, '>=', '>='],
      [Token::ASSIGNMENT, '=', '='],
      [Token::ASSIGNMENT_PLUS, '+=', '+='],
      [Token::ASSIGNMENT_MINUS, '-=', '-='],
      [Token::ASSIGNMENT_MULTIPLY, '*=', '*='],
      [Token::ASSIGNMENT_DIVIDE, '/=', '/='],
      [Token::ASSIGNMENT_AND, '&=', '&='],
      [Token::ASSIGNMENT_OR, '|=', '|='],
      [Token::ASSIGNMENT_XOR, '^=', '^='],
      [Token::INCREMENT, '++', '++'],
      [Token::DECREMENT, '--', '--'],
      [Token::PLUS, '+', '+'],
      [Token::MINUS, '-', '-'],
      [Token::MULTIPLY, '*', '*'],
      [Token::DIVIDE, '/', '/'],
      [Token::INT_CONSTANT, '123', '123'],
      [Token::FLOAT_CONSTANT, '1.23', '1.23'],
      [Token::NULLISH, '??', '??'],
      [Token::LINE_COMMENT, '// abcd\n', '// abcd\n'],
      [Token::MULTI_LINE_COMMENT, '/* abcd\nefg */', '/* abcd\nefg */'],
      [Token::CURLY_BRACKETS_OPEN, '{', '{'],
      [Token::CURLY_BRACKETS_CLOSED, '}', '}'],
      [Token::SQUARE_BRACKETS_OPEN, '[', '['],
      [Token::SQUARE_BRACKETS_CLOSED, ']', ']'],
      [Token::BRACKETS_OPEN, '(', '('],
      [Token::BRACKETS_CLOSED, ')', ')'],
      [Token::COMMA, ',', ','],
      [Token::SEMICOLON, ';', ';'],
      [Token::SCOPE_RESOLUTION, '::', '::'],
      [Token::INTL_BACKSLASH, '|', '|'],
      [Token::SPREAD, '...', '...'],
      [Token::DOT, '.', '.'],
      [Token::STRING_CONSTANT, '"ABC123!"', 'ABC123!'],
      [Token::IDENTIFIER, 'abc', 'abc'],
      [Token::MODULO, '% ', '%'],
    ];
  }
  // @formatter:on
  public function testTokens(): void {
    $tokens = static::getTokens();
    foreach($tokens as $token) {
      $tokenized = Tokenizer::tokenize($token[1]);
      $this->assertEquals($token[0], $tokenized->id);
      $this->assertEquals($token[2], $tokenized->value);
      $this->assertFalse($tokenized->hasNext());
    }
  }
}

