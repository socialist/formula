<?php
namespace TimoLehnertz\formula\tokens;

/**
 *
 * @author timo
 *        
 */
class Tokenizer {

  /**
   * Name that this tokenizer will generate
   *
   * @var string
   */
  private string $name;

  /**
   * Buffer of text that will be send to the new token
   *
   * @var string
   */
  private string $buffer = "";

  /**
   * Regex that needs to match this token.
   * Has to return false at any position of a string that will not be this token
   *
   * @var string
   */
  private string $regex;

  /**
   * Storing if this tokenizer is currently valid
   *
   * @var bool
   */
  private bool $valid = false;

  private int $position;
  
  private bool $allowWhiteSpaces;

  /**
   *
   * @param string $name
   * @param string $regex
   */
  public function __construct(string $name, string $regex, bool $allowWhitespaces = false) {
    $this->name = $name;
    $this->regex = $regex;
    $this->allowWhiteSpaces = $allowWhitespaces;
  }

  /**
   *
   * @param string $char the char to be parsed
   * @param int $position the global position inside the formula. Only used for Exeption messages
   * @return bool true when parsing was succsessful
   */
  public function parse(string $char, int $position): bool {
    if(!$this->allowWhiteSpaces && ctype_space($char)) return false;
    $matches = [];
    $this->buffer .= $char;
    $this->position = $position;
    preg_match($this->regex, $this->buffer, $matches);
    if($matches && $matches[0] == $this->buffer) {
      $this->valid = true;
    } else {
      if($this->valid) {
        return true;
      }
    }
    return false;
  }

  /**
   * Has to get called once the end of text has been reached.
   * Behaves identical to parse
   *
   * @param int $position the position of the last character
   */
  public function parseEndOfinput(int $position) {
    $this->buffer .= ' ';
    $this->position = $position;
    return $this->valid;
  }

  /**
   * Resets this tokenizer to a fresh start
   */
  public function reset(): void {
    $this->buffer = "";
    $this->valid = false;
  }

  /**
   * Gets all tokenizers
   *
   * @return array<Tokenizer>
   */
  public static function getPrimitiveTokenizers(): array {
    return [
      new Tokenizer("B", "/true|false/i"), // boolean
      new Tokenizer(":", "/:/"), // ternary :
      new Tokenizer("?", "/\?/"), // ternary ?
      new Tokenizer("O", "/[+\-*\/^]|&&|\|\||!=|!|==|<=|<|>=|>/"), // operator
      new Tokenizer("A", "/(?<!=)=(?!=)/"), // assignment
      new Tokenizer("++", "/\+\+/"), // increment
      new Tokenizer("--", "/--/"), // decrement
      new Tokenizer("N", "/\d+([\.]\d+)/"), // positive number
      new Tokenizer("null", "/null/"), // identifier
      new Tokenizer("I", "/[a-zA-Z][\w\d.]*/"), // identifier
      new Tokenizer("(", "/\(/"), // brackets opened
      new Tokenizer(")", "/\)/"), // brackets closed
      new Tokenizer("{", "/{/"), // brackets opened
      new Tokenizer("}", "/}/"), // brackets closed
      new Tokenizer("[", "/\[/"), // brackets opened
      new Tokenizer("]", "/\]/"), // brackets closed
      new Tokenizer(",", "/,/"), // comma
      new Tokenizer("S", '/("[^"]*"?)|(\'[^\']*\'?)/', true), // String literal "string" or 'string'
      new Tokenizer(";", '/;/', true) // statement delimiter
    ];
  }

  /**
   * Creates a token from current input
   *
   * @return Token
   */
  public function getParsedToken(): Token {
    return new Token($this->name, substr($this->buffer, 0, -1), $this->position);
  }
}