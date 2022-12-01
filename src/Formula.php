<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\expression\MathExpression;
use socialist\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\tokens\Tokenizer;

/**
 * 
 * @author Timo Lehnertz
 *
 */
class Formula {
  
  /**
   * String containing the original input to this formula
   *
   * @var string
   */
  protected ?string $source = '';
  
  /**
   * Array of tokens that have been found in tokenizing stage
   *
   * @var array
   */
  private array $tokens = [];
  
  /**
   * The top level math expression
   * @var MathExpression
   */
  private MathExpression $expression;

  public function __construct(string $source) {
    $this->source = $source;
    $this->tokens = Formula::tokenize(Formula::clearComments($source));
//     print_r($this->tokens);
    $this->expression = new MathExpression();
    $this->parse();
    $this->validate();
    $this->initDefaultMethods();
  }
  
  /**
   * Will set all variables with the given identifier to a value
   *
   * @param string $identifier the variable name
   * @param float $value
   */
  public function setVariable(string $identifier, float $value): void {
    $this->expression->setVariable($identifier, $value);
  }
  
  /**
   * Will set all methods with this identifier
   *
   * @param string $identifier
   */
  public function setMethod(string $identifier, callable $method): void {
    $this->expression->setMethod($identifier, $method);
  }

  public function calculate() {
    return $this->expression->calculate()->getValue();
  }
  
  private function parse(): void {
    $index = 0;
    $this->expression->parse($this->tokens, $index);
    if($index != sizeof($this->tokens)) {
      throw new ExpressionNotFoundException("Unexpected end of input");
    }
  }
  
  /**
   * Validate formula
   */
  private function validate(): void {
    $this->expression->validate(true);
  }
  
  /**
   * Converts a string into Tokens
   *
   * @param string $string
   * @throws ExpressionNotFoundException in case of failed tokenizing
   * @return array<Token>
   */
  private static function tokenize(string $string): array {
    $tokenizers = Tokenizer::getPrimitiveTokenizers();
    $tokens = [];
    $chars = str_split($string);
    for($i = 0;$i < sizeof($chars);$i++) {
      $char = $chars[$i];
      foreach($tokenizers as $tokenizer) {
        if(!$tokenizer->parse($char, $i)) continue;
        $tokens[] = $tokenizer->getParsedToken();
        foreach($tokenizers as $tokenizer)
          $tokenizer->reset();
          $i--;
          break;
      }
    }
    $parsedEnd = false;
    foreach($tokenizers as $tokenizer) {
      if($tokenizer->parseEndOfinput(sizeof($chars) - 1)) {
        $tokens[] = $tokenizer->getParsedToken();
        $parsedEnd = true;
        break;
      }
    }
    if(!$parsedEnd) {
      throw new ExpressionNotFoundException("Unexpected end of input");
    }
    return $tokens;
  }
  
  /**
   * Clear all comments in a string
   *
   * @param $source
   * @return string
   */
  private static function clearComments(string $source): string {
    $patterns = [
      '/\/\*(.*)\*\//i',
      '/\{(.*)\}/i',
      '/\[(.*)\]/i'
    ];
    return preg_replace($patterns, '', $source);
  }
  
  public function minFunc(...$values) {
    return min($values);
  }

  public function maxFunc(...$values) {
    return max($values);
  }
  
  public function powFunc($base, $exp) {
    return pow($base, $exp);
  }
  
  public function sqrtFunc(float $arg) {
    return sqrt($arg);
  }
  
  public function ceilFunc(float $value) {
    return ceil($value);
  }
  
  public function floorFunc(float $value) {
    return floor($value);
  }
  
  public function roundFunc(float $val, int $precision = null, int $mode = null) {
    return round($val, $precision, $mode);
  }
  
  public function sinFunc(float $arg) {
    return sin($arg);
  }
  
  public function cosFunc(float $arg) {
    return cos($arg);
  }
  
  public function tanFunc(float $arg) {
    return tan($arg);
  }

  public function is_nanFunc(float $val) {
    return is_nan($val);
  }

  public function absFunc(float $number) {
    return abs($number);
  }
  
  private function initDefaultMethods(): void {
    $this->setMethod("min", [$this, "minFunc"]);
    $this->setMethod("max", [$this, "maxFunc"]);
    $this->setMethod("pow", [$this, "powFunc"]);
    $this->setMethod("sqrt", [$this, "sqrtFunc"]);
    $this->setMethod("ceil", [$this, "ceilFunc"]);
    $this->setMethod("floor", [$this, "floorFunc"]);
    $this->setMethod("round", [$this, "roundFunc"]);
    $this->setMethod("sin", [$this, "sinFunc"]);
    $this->setMethod("cos", [$this, "cosFunc"]);
    $this->setMethod("tan", [$this, "tanFunc"]);
    $this->setMethod("is_nan", [$this, "is_nanFunc"]);
    $this->setMethod("abs", [$this, "absFunc"]);
  }
}