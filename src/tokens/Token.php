<?php
namespace TimoLehnertz\formula\tokens;

use TimoLehnertz\formula\UnexpectedEndOfInputException;

/**
 * @author Timo Lehnertz
 *
 */
class Token {

  public readonly int $id;

  public readonly string $value;

  public readonly int $line;

  public readonly int $position;

  private ?Token $prev = null;

  private ?Token $next = null;

  public function __construct(int $id, string $value, int $line, int $position, ?Token $prev) {
    $this->id = $id;
    $this->value = $value;
    $this->line = $line;
    $this->position = $position;
    $this->prev = $prev;
    if($prev !== null) {
      $prev->next = $this;
    }
  }

  public function hasNext(): bool {
    return $this->next !== null;
  }

  public function requireNext(): Token {
    if($this->next === null) {
      throw new UnexpectedEndOfInputException();
    }
    return $this->next;
  }

  public function next(): ?Token {
    return $this->next;
  }

  public const KEYWORD_INT = 0;

  public const KEYWORD_FLOAT = 1;

  public const KEYWORD_STRING = 2;

  public const KEYWORD_BOOL = 3;

  public const KEYWORD_CHAR = 4;

  public const KEYWORD_NEW = 5;

  public const KEYWORD_RETURN = 6;

  public const KEYWORD_CONTINUE = 7;

  public const KEYWORD_BREAK = 8;

  public const KEYWORD_TRUE = 9;

  public const KEYWORD_FALSE = 10;

  public const KEYWORD_VOID = 11;

  public const KEYWORD_NULL = 12;

  public const KEYWORD_IF = 13;

  public const KEYWORD_WHILE = 14;

  public const KEYWORD_DO = 15;

  public const KEYWORD_FOR = 16;

  public const COlON = 17;

  public const QUESTIONMARK = 18;

  public const LOGICAL_AND = 19;

  public const LOGICAL_OR = 20;

  public const LOGICAL_XOR = 21;

  public const EXCLAMATION_MARK = 22;

  public const COMPARISON_EQUALS = 23;

  public const COMPARISON_NOT_EQUALS = 24;

  public const COMPARISON_SMALLER = 25;

  public const COMPARISON_SMALLER_EQUALS = 26;

  public const COMPARISON_GREATER = 27;

  public const COMPARISON_GREATER_EQUALS = 28;

  public const ASSIGNMENT = 29;

  public const ASSIGNMENT_PLUS = 30;

  public const ASSIGNMENT_MINUS = 31;

  public const ASSIGNMENT_MULTIPLY = 32;

  public const ASSIGNMENT_DIVIDE = 33;

  public const ASSIGNMENT_AND = 34;

  public const ASSIGNMENT_OR = 35;

  public const ASSIGNMENT_XOR = 36;

  public const INCREMENT = 37;

  public const DECREMENT = 38;

  public const PLUS = 39;

  public const MINUS = 40;

  public const MULTIPLY = 41;

  public const DIVIDE = 42;

  public const INT_CONSTANT = 44;

  public const FLOAT_CONSTANT = 45;

  public const NULLISH = 46;

  public const LINE_COMMENT = 47;

  public const MULTI_LINE_COMMENT = 48;

  public const CURLY_BRACKETS_OPEN = 49;

  public const CURLY_BRACKETS_CLOSED = 50;

  public const SQUARE_BRACKETS_OPEN = 51;

  public const SQUARE_BRACKETS_CLOSED = 52;

  public const BRACKETS_OPEN = 53;

  public const BRACKETS_CLOSED = 54;

  public const COMMA = 55;

  public const SEMICOLON = 56;

  public const SCOPE_RESOLUTION = 57;

  // |
  public const INTL_BACKSLASH = 58;

  // ...
  public const SPREAD = 59;

  public const DOT = 60;

  public const STRING_CONSTANT = 61;

  public const IDENTIFIER = 62;
}