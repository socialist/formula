<?php
namespace TimoLehnertz\formula\tokens;

/**
 * @author Timo Lehnertz
 *
 */
class Tokenizer {

  private static function isValidForIdentifier(string $char): bool {
    return ctype_alpha($char) || ctype_digit($char) || $char === '_';
  }

  /**
   * Converts a string into Tokens
   *
   * @param string $string
   * @return array<Token>
   */
  public static function tokenize(string $string): ?Token {
    $string = str_replace("\r\n", "\n", $string);
    $string = str_replace("\n\r", "\n", $string);
    $string = str_replace("\r", "\n", $string);
    $chars = str_split($string);
    $firstToken = null;
    $lastToken = null;
    $line = 0;
    $position = 0;
    $lastStartPosition = 0;
    $buffer = "";
    $mode = "normal";
    $keyword = null;
    $singleToken = null;
    $stringBoundry = null;
    $numberHasDot = false;
    $tokenStartLine = 0;
    $tokenSource = '';
    $addedToken = function () use (&$keyword, &$singleToken, &$mode, &$buffer, &$stringBoundry, &$numberHasDot, &$firstToken, &$lastToken, &$lastStartPosition, &$position, &$tokenStartLine, &$line, &$tokenSource) {
      $keyword = null;
      $singleToken = null;
      $mode = "normal";
      $buffer = "";
      $stringBoundry = null;
      $numberHasDot = false;
      $lastStartPosition = $position;
      $tokenStartLine = $line;
      $tokenSource = '';
      if($firstToken === null) {
        $firstToken = $lastToken;
      }
    };
    for($i = 0;$i <= sizeof($chars);$i++) {
      // keep track of position
      $position++;
      if(isset($chars[$i])) {
        $tokenSource .= $chars[$i];
      }
      $char = $chars[$i] ?? "\n";
      if($char === "\n") {
        $line++;
        $lastStartPosition = 0;
        $position = 0;
      }
      switch($mode) {
        // skip white spaces
        case 'normal':
          if(strlen($buffer) === 0 && ctype_space($char)) {
            break;
          }
          $buffer .= $char;
          // check comments
          if($buffer === Tokenizer::LINE_COMMENT_1 || $buffer === Tokenizer::LINE_COMMENT_2) {
            $mode = "lineComment";
            break;
          }
          if($buffer === Tokenizer::MULTI_LINE_COMMENT_START) {
            $mode = "multiComment";
            break;
          }
          // check instant tokens
          if(array_key_exists($buffer, Tokenizer::INSTANT_TOKENS)) {
            $lastToken = new Token(Tokenizer::INSTANT_TOKENS[$buffer], $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            $addedToken();
            break;
          }
          // check keywords
          if($keyword === null) {
            if(array_key_exists($buffer, Tokenizer::KEYWORD_TOKENS)) {
              $keyword = Tokenizer::KEYWORD_TOKENS[$buffer];
            }
          } else {
            if(!static::isValidForIdentifier($char)) {
              $lastToken = new Token($keyword, substr($buffer, 0, strlen($buffer) - strlen($char)), $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
              $i--;
              $position--;
              $addedToken();
              break;
            }
            $keyword = null;
          }
          // check single tokens
          if($singleToken === null) {
            if(array_key_exists($buffer, Tokenizer::SINGLE_TOKENS)) {
              $singleToken = $buffer;
              break;
            }
          } else {
            if($char !== '=' && $char !== $singleToken) {
              $lastToken = new Token(Tokenizer::SINGLE_TOKENS[$singleToken], $singleToken, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
              $i--;
              $position--;
              $addedToken();
              break;
            }
            $singleToken = null;
          }
          // check string
          if(strlen($buffer) === 1 && $char === '"' || $char === "'") {
            $stringBoundry = $char;
            $mode = 'string';
            $buffer = '';
            break;
          }
          // check identifier
          if(ctype_alpha($buffer[0]) && strlen($buffer) > 1) {
            if(!static::isValidForIdentifier($char)) {
              $lastToken = new Token(Token::IDENTIFIER, substr($buffer, 0, strlen($buffer) - strlen($char)), $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
              $i--;
              $position--;
              $addedToken();
              break;
            }
          }
          // check number
          if(strlen($buffer) === 1 && ctype_digit($char)) {
            $mode = 'number';
            break;
          }
          if($char === "\n") {
            $tokenStartLine = $line;
          }
          break;
        case 'lineComment':
          if($char === "\n") {
            $lastToken = new Token(Token::LINE_COMMENT, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            $addedToken();
            break;
          }
          $buffer .= $char;
          break;
        case 'multiComment':
          $buffer .= $char;
          if(str_ends_with($buffer, Tokenizer::MULTI_LINE_COMMENT_END)) {
            $lastToken = new Token(Token::MULTI_LINE_COMMENT, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            $addedToken();
            break;
          }
          break;
        case 'string':
          if($char === $stringBoundry) {
            /**
             * Supported formats:
             *
             * P0Y1M2DT3H4M5S
             * P0Y1M2WT3H4M5S
             * P0Y1M2DT3H
             * P0YT3H4M5S
             * P0YT3H
             * P0Y1M2D
             * P0Y
             */
            $dateIntervalRegex = '/^P((((([0-9]+Y([0-9]+M)?([0-9]+[DW])?)|([0-9]+M([0-9]+[DW])?)|([0-9]+[DW]))(T(([0-9]+H([0-9]+M)?([0-9]+S)?)|([0-9]+M([0-9]+S)?)|([0-9]+S)))?|(T(([0-9]+H([0-9]+M)?([0-9]+S)?)|([0-9]+M([0-9]+S)?)|([0-9]+S)))))|(([0-9]{4}-(0[0-9]|1[0-2])-([0-2][0-9]|3[0-1]))T(([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])))$/';
            /**
             * Supported formats:
             *
             * 2008-09-15T15:53:00
             * 2008-09-15
             * 2008-09-15
             * 2007-03-01T13:00:00Z
             * 2015-10-05T21:46:54+00:00
             * 2015-10-05T21:46:54Z
             * 2008-09-15 11:12:13
             * 2008-09-15 11:12
             * 2008-09
             * 1988-05-26T23:00:00.000Z
             */
            $dateTimeRegex = '/^(?P<y>\d{4})
-
(?P<m>\d{2})
(?:
  -
  (?P<d>\d{2})
)?
(?:
  [T\s]
  (?P<hh>\d{2})\:
  (?P<mm>\d{2})
  (?:\:(?P<ss>\d{2}))?
  (?:[+-](?P<tz>\d{2}\:\d{2}))?
  (?:
   (?:\.(?P<u>\d{2,4}))?[Z]
  )?
)?$/ix';
            if(preg_match($dateIntervalRegex, $buffer)) {
              $lastToken = new Token(Token::DATE_INTERVAL, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            } else if(preg_match($dateTimeRegex, $buffer)) {
              $lastToken = new Token(Token::DATE_TIME, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            } else {
              $lastToken = new Token(Token::STRING_CONSTANT, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            }
            $addedToken();
            break;
          }
          $buffer .= $char;
          break;
        case 'number':
          if($char === '.') {
            if($numberHasDot) {
              throw new TokenisationException('Number cant have two dots', $tokenStartLine, $lastStartPosition);
            }
            $numberHasDot = true;
          } else if(!ctype_digit($char)) {
            if(str_ends_with($buffer, '.')) {
              throw new TokenisationException('Incomplete number', $tokenStartLine, $lastStartPosition);
            }
            $lastToken = new Token($numberHasDot ? Token::FLOAT_CONSTANT : Token::INT_CONSTANT, $buffer, $tokenStartLine, $lastStartPosition, $tokenSource, $lastToken);
            $i--;
            $position--;
            $addedToken();
            break;
          }
          $buffer .= $char;
          break;
      }
    }
    if($mode !== 'normal') {
      throw new TokenisationException('Unexpected end of input', $line, $position);
    }
    return $firstToken;
  }

  private const LINE_COMMENT_1 = "//";

  private const LINE_COMMENT_2 = "#";

  private const MULTI_LINE_COMMENT_START = "/*";

  private const MULTI_LINE_COMMENT_END = "*/";

  // @formatter:off
  private const INSTANT_TOKENS = [
    "??" => Token::NULLISH,
    "&&" => Token::LOGICAL_AND,
    "||" => Token::LOGICAL_OR,

    "==" => Token::COMPARISON_EQUALS,
    ">=" => Token::COMPARISON_GREATER_EQUALS,
    "<=" => Token::COMPARISON_SMALLER_EQUALS,
    "!=" => Token::COMPARISON_NOT_EQUALS,

    "+=" => Token::ASSIGNMENT_PLUS,
    "-=" => Token::ASSIGNMENT_MINUS,
    "*=" => Token::ASSIGNMENT_MULTIPLY,
    "/=" => Token::ASSIGNMENT_DIVIDE,
    "&=" => Token::ASSIGNMENT_AND,
    "|=" => Token::ASSIGNMENT_OR,
    "^=" => Token::ASSIGNMENT_XOR,
    "++" => Token::INCREMENT,
    "--" => Token::DECREMENT,
    "%" => Token::MODULO,

    "{" => Token::CURLY_BRACKETS_OPEN,
    "}" => Token::CURLY_BRACKETS_CLOSED,
    "[" => Token::SQUARE_BRACKETS_OPEN,
    "]" => Token::SQUARE_BRACKETS_CLOSED,
    "(" => Token::BRACKETS_OPEN,
    ")" => Token::BRACKETS_CLOSED,
    "," => Token::COMMA,
    ";" => Token::SEMICOLON,
    "::" => Token::SCOPE_RESOLUTION,
    "..." => Token::SPREAD,
    "->" => Token::FUNCTION_ARROW
  ];

  private const KEYWORD_TOKENS = [
    "true" => Token::KEYWORD_TRUE,
    "false" => Token::KEYWORD_FALSE,
    "int" => Token::KEYWORD_INT,
    "float" => Token::KEYWORD_FLOAT,
    "String" => Token::KEYWORD_STRING,
    "boolean" => Token::KEYWORD_BOOL,
    "new" => Token::KEYWORD_NEW,
    "char" => Token::KEYWORD_CHAR,
    "return" => Token::KEYWORD_RETURN,
    "continue" => Token::KEYWORD_CONTINUE,
    "break" => Token::KEYWORD_BREAK,
    "void" => Token::KEYWORD_VOID,
    "null" => Token::KEYWORD_NULL,
    "if" => Token::KEYWORD_IF,
    "while" => Token::KEYWORD_WHILE,
    "do" => Token::KEYWORD_DO,
    "for" => Token::KEYWORD_FOR,
    "instanceof" => Token::KEYWORD_INSTANCEOF,
    "Type" => Token::KEYWORD_TYPE,
    "else" => Token::KEYWORD_ELSE,
    "final" => Token::KEYWORD_FINAL,
    "var" => Token::KEYWORD_VAR,
    "DateTimeImmutable" => Token::KEYWORD_DATE_TIME_IMMUTABLE,
    "DateInterval" => Token::KEYWORD_DATE_INTERVAL,
  ];

  private
const SINGLE_TOKENS = ["+" => Token::PLUS,"-" => Token::MINUS,"*" => Token::MULTIPLY,"/" => Token::DIVIDE,"|" => Token::INTL_BACKSLASH,"?" => Token::QUESTIONMARK,"." => Token::DOT,"=" => Token::ASSIGNMENT,":" => Token::COlON,"^" => Token::LOGICAL_XOR,"<" => Token::COMPARISON_SMALLER,">" => Token::COMPARISON_GREATER,"!" => Token::EXCLAMATION_MARK];
  // @formatter:on
}
