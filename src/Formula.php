<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\expression\MathExpression;
use TimoLehnertz\formula\expression\Method;
use TimoLehnertz\formula\expression\StringLiteral;
use TimoLehnertz\formula\expression\Variable;
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
    $this->expression = new MathExpression();
    $this->parse();
    $this->validate();
    $this->initDefaultMethods();
  }
  
  /**
   * Will set all variables with the given identifier to a value
   *
   * @param string $identifier the variable name
   * @param mixed $value
   */
  public function setVariable(string $identifier, $value): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Variable) {
        if($content->getIdentifier() == $identifier) {
          $content->setValue(Method::calculateableFromValue($value));
        }
      }
    }
  }
  
  public function resetVariable(string $identifier): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Variable) {
        if($content->getIdentifier() == $identifier) {
          $content->reset();
        }
      }
    }
  }
  
  /**
   * Will set all methods with this identifier
   *
   * @param string $identifier
   */
  public function setMethod(string $identifier, callable $method): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Method) {
        if($content->getIdentifier() === $identifier) $content->setMethod($method);
      }
    }
  }
  
  public function resetMethod(string $identifier): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Method) {
        if($content->getIdentifier() == $identifier) {
          $content->reset();
        }
      }
    }
    $this->initDefaultMethods(); // in case a buildin method got resetted
  }
  
  public function resetAllVariables(): void {
    foreach ($this->getVariables() as $variableIdentifier) {
      $this->resetVariable($variableIdentifier);
    }
  }
  
  public function resetAllMethods(): void {
    foreach ($this->getMethodIdentifiers() as $methodIdentifiers) {
      $this->resetMethod($methodIdentifiers);
    }
  }
  
  /**
   * @param string $oldName
   * @param string $newName
   */
  public function renameVariables(string $oldName, string $newName, bool $caseSensitive = true): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Variable) {
        if(self::strcmp($content->getIdentifier(), $oldName, $caseSensitive)) $content->setIdentifier($newName);
      }
    }
  }
  
  /**
   * @param string $oldName
   * @param string $newName
   */
  public function renameStrings(string $oldName, string $newName, bool $caseSensitive = true): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof StringLiteral) {
        if(self::strcmp($content->getValue(), $oldName, $caseSensitive)) $content->setValue($newName);
      }
    }
  }
  
  /**
   * @param string $oldName
   * @param string $newName
   */
  public function renameMethods(string $oldName, string $newName, bool $caseSensitive = true): void {
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Method) {
        if(self::strcmp($content->getIdentifier(), $oldName, $caseSensitive)) $content->setIdentifier($newName);
      }
    }
    $this->initDefaultMethods(); // in case a method got renamed to a buildin method
  }
  
  /**
   * @param string $a
   * @param string $b
   * @param bool $caseSensitive
   * @return bool equal
   */
  private static function strcmp(string $a, string $b, bool $caseSensitive): bool {
    if($caseSensitive) return $a === $b;
    return strcasecmp($a, $b) == 0;
  }
  
  /**
   * Calculates and returnes the result of this formula
   * @return mixed
   */
  public function calculate() {
    return $this->expression->calculate()->getValue();
  }
  
  private function parse(): void {
    $index = 0;
    $this->expression->parse($this->tokens, $index);
    if($index != sizeof($this->tokens)) {
      throw new ExpressionNotFoundException("Unexpected end of input", $this->source);
    }
  }
  
  /**
   * Validate formula
   */
  private function validate(): void {
    $this->expression->setInsideBrackets(false); //for sure not needed
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
      throw new ExpressionNotFoundException("Unexpected end of input", $string);
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
    ];
    return preg_replace($patterns, '', $source);
  }
  
  /**
   * Gets all string literals
   * @return array<string>
   */
  public function getStringLiterals(): array {
    $strings = [];
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof StringLiteral) $strings[] = $content->getValue();
    }
    return $strings;
  }
  
  /**
   * Gets all method identifiers
   * @return array<string>
   */
  public function getMethodIdentifiers(): array {
    $methods = [];
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Method) $methods[] = $content;
    }
    $identifiers = [];
    foreach ($methods as $method) {
      if(!in_array($method->getIdentifier(), $identifiers)) {
        $identifiers []= $method->getIdentifier();
      }
    }
    return $identifiers;
  }
  
  /**
   * Gets all variable identifiers present in this formula
   * @return string[]
   */
  public function getVariables(): array {
    $variables = [];
    foreach ($this->expression->getContent() as $content) {
      if($content instanceof Variable) $variables[] = $content;
    }
    $identifiers = [];
    foreach ($variables as $variable) {
      if(!in_array($variable->getIdentifier(), $identifiers)) {
        $identifiers []= $variable->getIdentifier();
      }
    }
    return $identifiers;
  }
  
  /**
   * Merges an array of arrays into one flat array (Recursively)
   * @param array $arrays
   * @return array
   */
  private static function mergeArraysRecursive($arrays): array {
    $merged = [];
    foreach ($arrays as $val) {
      if(is_array($val)) {
        $merged = array_merge($merged, Formula::mergeArraysRecursive($val));
      } else {
        $merged[] = $val;
      }
    }
    return $merged;
  }
  
  public function minFunc(...$values) {
    $values = Formula::mergeArraysRecursive($values);
    return min($values);
  }
  
  public function maxFunc(...$values) {
    $values = Formula::mergeArraysRecursive($values);
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
  
  public function asVectorFunc(...$values) {
    return $values;
  }
  
  public function sizeofFunc(...$values) {
    $count = 0;
    foreach ($values as $value) {
      if(is_array($value)) {
        $count += $this->sizeofFunc(...$value);
      } else {
        $count++;
      }
    }
    return $count;
  }
  
  public function inRangeFunc(float $value, float $min, float $max): bool {
    return ($min <= $value) && ($value <= $max);
  }
  
  public function reduceFunc(array $values, array $filter): array {
    $result = [];
    foreach ($values as $value) {
      if(in_array($value, $filter)) {
        $result []= $value;
      }
    }
    return $result;
  }
  
  public function firstOrNullFunc($array) {
    if(sizeof($array) === 0) return null;
    return $array[0];
  }
  
  /**
   * @param float[] $values
   * @return number sum of all numeric members in $values 
   */
  public function sumFunc(...$values) {
    $res = 0;
    foreach ($values as $value) {
      if(is_numeric($value) && !is_string($value)) {
        $res += $value;
      } else if(is_array($value)) {
        $res += $this->sumFunc(...$value);
      } else {
        throw new \Exception('Only numeric values or vectors are allowed for sum');
      }
    }
    return $res;
  }

  /**
   * @param float[] $values
   * @return number sum of all numeric members in $values 
   */
  public function avgFunc(...$values) {
    $sum = $this->sumFunc($values);
    return $sum / $this->sizeofFunc($values);
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
    $this->setMethod("asVector", [$this, "asVectorFunc"]);
    $this->setMethod("sizeof", [$this, "sizeofFunc"]);
    $this->setMethod("inRange", [$this, "inRangeFunc"]);
    $this->setMethod("reduce", [$this, "reduceFunc"]);
    $this->setMethod("firstOrNull", [$this, "firstOrNullFunc"]);
    $this->setMethod("sum", [$this, "sumFunc"]);
    $this->setMethod("avg", [$this, "avgFunc"]);
  }
  
  /**
   * @return string source string
   */
  public function getSource(): string {
    return $this->source;
  }
  
  public function getFormula(): string {
    return $this->expression->toString();
  }
}