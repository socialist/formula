<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\tokens;

use TimoLehnertz\formula\parsing\ParsingException;
use const false;

/**
 * @author Timo Lehnertz
 */
class Token {

  public readonly int $id;

  public readonly string $value;

  public readonly int $line;

  public readonly int $position;

  public readonly string $source;

  private ?Token $prev = null;

  private ?Token $next = null;

  public function __construct(int $id, string $value, int $line, int $position, string $source, ?Token $prev) {
    $this->id = $id;
    $this->value = $value;
    $this->line = $line;
    $this->position = $position;
    $this->prev = $prev;
    $this->source = $source;
    if($prev !== null) {
      $prev->next = $this;
    }
  }

  public function hasPrev(bool $includeComments = false): bool {
    return $this->prev($includeComments) !== null;
  }

  public function prev(bool $includeComments = false): ?Token {
    if($includeComments) {
      return $this->prev;
    } else if($this->prev !== null) {
      if($this->prev->id === static::LINE_COMMENT || $this->prev->id === static::MULTI_LINE_COMMENT) {
        return $this->prev->prev($includeComments);
      } else {
        return $this->prev;
      }
    }
    return null;
  }

  public function hasNext(bool $includeComments = false): bool {
    return $this->next($includeComments) !== null;
  }

  public function skipComment(): ?Token {
    if($this->id !== static::LINE_COMMENT && $this->id !== static::MULTI_LINE_COMMENT) {
      return $this;
    } else {
      return $this->next();
    }
  }

  public function next(bool $includeComments = false): ?Token {
    if($includeComments) {
      return $this->next;
    } else if($this->next !== null) {
      if($this->next->id === static::LINE_COMMENT || $this->next->id === static::MULTI_LINE_COMMENT) {
        return $this->next->next($includeComments);
      } else {
        return $this->next;
      }
    }
    return null;
  }

  public function requireNext(bool $includeComments = false): Token {
    $next = $this->next($includeComments);
    if($next === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT, $this);
    } else {
      return $next;
    }
  }

  public function last(bool $includeComments = false): Token {
    $next = $this->next($includeComments);
    if($next === null) {
      return $this;
    } else {
      return $next->last($includeComments);
    }
  }

  // @formatter:off
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
  public const INTL_BACKSLASH = 58; // |
  public const SPREAD = 59;
  public const DOT = 60;
  public const STRING_CONSTANT = 61;
  public const IDENTIFIER = 62;
  public const MODULO = 63;
  public const KEYWORD_INSTANCEOF = 64;
  public const KEYWORD_TYPE = 65;
  public const KEYWORD_ELSE = 66;
  public const KEYWORD_FINAL = 67;
  public const KEYWORD_VAR = 68;
  public const DATE_INTERVAL = 69;
  public const DATE_TIME = 70;
  public const KEYWORD_DATE_TIME_IMMUTABLE = 71;
  public const KEYWORD_DATE_INTERVAL = 72;
  // @formatter:on
}
