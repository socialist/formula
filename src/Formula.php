<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\parsing\CodeBlockParser;
use TimoLehnertz\formula\procedure\Method;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlock;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\BooleanValue;
use TimoLehnertz\formula\type\FloatValue;
use TimoLehnertz\formula\type\IntegerValue;
use TimoLehnertz\formula\type\StringValue;
use TimoLehnertz\formula\type\Value;

/**
 * This class represents a formula session that can interpret/run code
 *
 * @author Timo Lehnertz
 *
 */
class Formula {

  private readonly CodeBlock $codeBlock;

  /**
   * The global scope containing all local scopes
   */
  private readonly Scope $parentScope;

  private readonly FormulaSettings $formulaSettings;

  private readonly string $source;

  public function __construct(string $source, ?Scope $parentScope = null, ?FormulaSettings $formulaSettings = null) {
    $this->source = $source;
    if($formulaSettings === null) {
      $formulaSettings = FormulaSettings::buildDefaultSettings();
    }
    $this->formulaSettings = $formulaSettings;
    $this->parentScope = $parentScope ?? new Scope();
    $firstToken = Tokenizer::tokenize($source);
    if($firstToken === null) {
      throw new FormulaValidationException();
    }
    $parsedCodeblock = (new CodeBlockParser(true))->parse($firstToken);
    $this->codeBlock = $parsedCodeblock->parsed;
    $this->codeBlock->validate($this->buildDefaultScope());
  }

  private static function valueByPHPVar(mixed $value): Value {
    if(is_int($value)) {
      return new IntegerValue($value);
    } else if(is_float($value)) {
      return new FloatValue($value);
    } else if(is_bool($value)) {
      return new BooleanValue($value);
    } else if(is_string($value)) {
      return new StringValue($value);
    }
    throw new FormulaRuntimeException($value.' has no supported php type');
  }

  /**
   * @param string $oldName
   * @param string $newName
   */
  //   public function renameVariables(string $oldName, string $newName, bool $caseSensitive = true): void {
  //     foreach($this->expression->getContent() as $content) {
  //       if($content instanceof Variable) {
  //         if(self::strcmp($content->getIdentifier(), $oldName, $caseSensitive))
  //           $content->setIdentifier($newName);
  //       }
  //     }
  //   }

  /**
   * @param string $oldName
   * @param string $newName
   */
  //   public function renameStrings(string $oldName, string $newName, bool $caseSensitive = true): void {
  //     foreach($this->expression->getContent() as $content) {
  //       if($content instanceof StringLiteral) {
  //         if(self::strcmp($content->getValue(), $oldName, $caseSensitive))
  //           $content->setValue($newName);
  //       }
  //     }
  //   }

  /**
   * @param string $oldName
   * @param string $newName
   */
  public function renameMethods(string $oldName, string $newName, bool $caseSensitive = true): void {
    foreach($this->expression->getContent() as $content) {
      if($content instanceof Method) {
        if(self::strcmp($content->getIdentifier(), $oldName, $caseSensitive))
          $content->setIdentifier($newName);
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
    if($caseSensitive)
      return $a === $b;
    return strcasecmp($a, $b) == 0;
  }

  /**
   * Calculates and returnes the result of this formula
   *
   * @return mixed
   */
  public function calculate(): Value {
    return $this->codeBlock->run($this->buildDefaultScope())->returnValue;
  }

  private function parse(): void {
    $index = 0;
    $this->expression->parse($this->tokens, $index);
    if($index != sizeof($this->tokens)) {
      throw new ExpressionNotFoundException("Unexpected end of input", $this->source);
    }
  }

  /**
   * Gets all string literals
   *
   * @return array<string>
   */
  //   public function getStringLiterals(): array {
  //     $strings = [];
  //     foreach($this->expression->getContent() as $content) {
  //       if($content instanceof StringLiteral)
  //         $strings[] = $content->getValue();
  //     }
  //     return $strings;
  //   }

  /**
   * Gets all method identifiers
   *
   * @return array<string>
   */
  public function getMethodIdentifiers(): array {
    $methods = [];
    foreach($this->expression->getContent() as $content) {
      if($content instanceof Method)
        $methods[] = $content;
    }
    $identifiers = [];
    foreach($methods as $method) {
      if(!in_array($method->getIdentifier(), $identifiers)) {
        $identifiers[] = $method->getIdentifier();
      }
    }
    return $identifiers;
  }

  /**
   * Gets all variable identifiers present in this formula
   *
   * @return string[]
   */
  //   public function getVariables(): array {
  //     $variables = [];
  //     foreach($this->expression->getContent() as $content) {
  //       if($content instanceof Variable)
  //         $variables[] = $content;
  //     }
  //     $identifiers = [];
  //     foreach($variables as $variable) {
  //       if(!in_array($variable->getIdentifier(), $identifiers)) {
  //         $identifiers[] = $variable->getIdentifier();
  //       }
  //     }
  //     return $identifiers;
  //   }

  /**
   * Merges an array of arrays into one flat array (Recursively)
   *
   * @param array $arrays
   * @return array
   */
  private static function mergeArraysRecursive($arrays): array {
    $merged = [];
    foreach($arrays as $val) {
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
    foreach($values as $value) {
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
    foreach($values as $value) {
      if(in_array($value, $filter)) {
        $result[] = $value;
      }
    }
    return $result;
  }

  public function firstOrNullFunc($array) {
    if(sizeof($array) === 0)
      return null;
    return $array[0];
  }

  /**
   * @param float[] $values
   * @return number sum of all numeric members in $values
   */
  public function sumFunc(...$values) {
    $res = 0;
    foreach($values as $value) {
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

  private function buildDefaultScope(): Scope {
    $scope = $this->parentScope->buildChild();
    //     $scope->defineMethod(new Method("min", [$this,"minFunc"]));
    //     $scope->defineMethod(new Method("max", [$this,"maxFunc"]));
    //     $scope->defineMethod(new Method("pow", [$this,"powFunc"]));
    //     $scope->defineMethod(new Method("sqrt", [$this,"sqrtFunc"]));
    //     $scope->defineMethod(new Method("ceil", [$this,"ceilFunc"]));
    //     $scope->defineMethod(new Method("floor", [$this,"floorFunc"]));
    //     $scope->defineMethod(new Method("round", [$this,"roundFunc"]));
    //     $scope->defineMethod(new Method("sin", [$this,"sinFunc"]));
    //     $scope->defineMethod(new Method("cos", [$this,"cosFunc"]));
    //     $scope->defineMethod(new Method("tan", [$this,"tanFunc"]));
    //     $scope->defineMethod(new Method("is_nan", [$this,"is_nanFunc"]));
    //     $scope->defineMethod(new Method("abs", [$this,"absFunc"]));
    //     $scope->defineMethod(new Method("asVector", [$this,"asVectorFunc"]));
    //     $scope->defineMethod(new Method("sizeof", [$this,"sizeofFunc"]));
    //     $scope->defineMethod(new Method("inRange", [$this,"inRangeFunc"]));
    //     $scope->defineMethod(new Method("reduce", [$this,"reduceFunc"]));
    //     $scope->defineMethod(new Method("firstOrNull", [$this,"firstOrNullFunc"]));
    //     $scope->defineMethod(new Method("sum", [$this,"sumFunc"]));
    return $scope;

    // $this->setMethod("min", [$this, "minFunc"]);
    // $this->setMethod("max", [$this, "maxFunc"]);
    // $this->setMethod("pow", [$this, "powFunc"]);
    // $this->setMethod("sqrt", [$this, "sqrtFunc"]);
    // $this->setMethod("ceil", [$this, "ceilFunc"]);
    // $this->setMethod("floor", [$this, "floorFunc"]);
    // $this->setMethod("round", [$this, "roundFunc"]);
    // $this->setMethod("sin", [$this, "sinFunc"]);
    // $this->setMethod("cos", [$this, "cosFunc"]);
    // $this->setMethod("tan", [$this, "tanFunc"]);
    // $this->setMethod("is_nan", [$this, "is_nanFunc"]);
    // $this->setMethod("abs", [$this, "absFunc"]);
    // $this->setMethod("asVector", [$this, "asVectorFunc"]);
    // $this->setMethod("sizeof", [$this, "sizeofFunc"]);
    // $this->setMethod("inRange", [$this, "inRangeFunc"]);
    // $this->setMethod("reduce", [$this, "reduceFunc"]);
    // $this->setMethod("firstOrNull", [$this, "firstOrNullFunc"]);
    // $this->setMethod("sum", [$this, "sumFunc"]);
    // $this->setMethod("avg", [$this, "avgFunc"]);
  }

  public function getFormula(): string {
    return $this->expression->toString();
  }
}