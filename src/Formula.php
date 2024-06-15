<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\parsing\CodeBlockOrExpressionParser;
use TimoLehnertz\formula\procedure\Method;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlockOrExpression;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use src\tokens\TokenisationException;

/**
 * This class represents a formula session that can interpret/run code
 *
 * @author Timo Lehnertz
 */
class Formula {

  private readonly CodeBlockOrExpression $content;

  private static Scope $inbuildScope;

  private readonly Scope $parentScope;

  private readonly FormulaSettings $formulaSettings;

  private readonly string $source;

  private readonly Type $returnType;

  public function __construct(string $source, ?Scope $parentScope = null, ?FormulaSettings $formulaSettings = null) {
    $this->source = $source;
    if(!isset(Formula::$inbuildScope)) {
      Formula::$inbuildScope = Formula::buildInbuiltScope();
    }
    if($formulaSettings === null) {
      $formulaSettings = FormulaSettings::buildDefaultSettings();
    }
    $this->formulaSettings = $formulaSettings;
    $this->parentScope = $parentScope ?? new Scope();
    $firstToken = Tokenizer::tokenize($source)->skipComment();
    if($firstToken === null) {
      throw new TokenisationException('Invalid formula', 0, 0);
    }
    $parsedContent = (new CodeBlockOrExpressionParser(true))->parse($firstToken, true, true);
    $this->content = $parsedContent->parsed;
    $this->returnType = $this->content->validate($this->buildDefaultScope())->returnType ?? new VoidType();
  }

  public function getNodeTree(): array {
    return $this->content->buildNode($this->buildDefaultScope());
  }

  public function getReturnType(): Type {
    return $this->returnType;
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
   */
  public function calculate(): Value {
    return $this->content->run($this->buildDefaultScope())->returnValue ?? new VoidValue();
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
  public static function printFunc(string $str): void {
    echo $str;
  }

  public static function printlnFunc(string $str): void {
    self::printFunc($str.PHP_EOL);
  }

  /**
   * Merges an array of arrays into one flat array (Recursively)
   *
   * @param array $arrays
   * @return array
   */
  private static function mergeArraysRecursive(array $arrays): array {
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

  public static function minFunc(float|array ...$values): float {
    $values = Formula::mergeArraysRecursive($values);
    return min($values);
  }

  public static function maxFunc(float|array ...$values): float {
    $values = Formula::mergeArraysRecursive($values);
    return max($values);
  }

  public static function powFunc(float $base, float $exp): float {
    return (float) pow($base, $exp);
  }

  public static function sqrtFunc(float $arg): float {
    return sqrt($arg);
  }

  public static function ceilFunc(float $value): float {
    return ceil($value);
  }

  public static function floorFunc(float $value): float {
    return floor($value);
  }

  public static function roundFunc(float $val, int $precision = null, int $mode = null): float {
    return round($val, $precision, $mode);
  }

  public static function sinFunc(float $arg): float {
    return sin($arg);
  }

  public static function cosFunc(float $arg): float {
    return cos($arg);
  }

  public static function tanFunc(float $arg): float {
    return tan($arg);
  }

  public static function is_nanFunc(float $val): float {
    return is_nan($val);
  }

  public static function absFunc(float $number): float {
    return abs($number);
  }

  public static function asVectorFunc(mixed ...$values): array {
    return $values;
  }

  public static function sizeofFunc(array|float ...$values): int {
    $count = 0;
    foreach($values as $value) {
      if(is_array($value)) {
        $count += static::sizeofFunc(...$value);
      } else {
        $count++;
      }
    }
    return $count;
  }

  public static function inRangeFunc(float $value, float $min, float $max): bool {
    return ($min <= $value) && ($value <= $max);
  }

  public static function reduceFunc(array $values, array $filter): array {
    $result = [];
    foreach($values as $value) {
      if(in_array($value, $filter)) {
        $result[] = $value;
      }
    }
    return $result;
  }

  public static function firstOrNullFunc(array $array): mixed {
    if(sizeof($array) === 0)
      return null;
    return $array[0];
  }

  /**
   * @param float[] $values
   * @return number sum of all numeric members in $values
   */
  public static function sumFunc(float|array ...$values): float {
    $res = 0;
    foreach($values as $value) {
      if(is_numeric($value) && !is_string($value)) {
        $res += $value;
      } else if(is_array($value)) {
        $res += static::sumFunc(...$value);
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
  public static function avgFunc(float|array ...$values) {
    $sum = self::sumFunc($values);
    return $sum / self::sizeofFunc($values);
  }

  private static function buildInbuiltScope(): Scope {
    $scope = new Scope();
    $scope->definePHPFunction('print', [Formula::class,'printFunc']);
    $scope->definePHPFunction('println', [Formula::class,'printlnFunc']);
    $scope->definePHPFunction('pow', [Formula::class,'powFunc']);
    $scope->definePHPFunction("min", [Formula::class,"minFunc"]);
    $scope->definePHPFunction("max", [Formula::class,"maxFunc"]);
    $scope->definePHPFunction("sqrt", [Formula::class,"sqrtFunc"]);
    $scope->definePHPFunction("ceil", [Formula::class,"ceilFunc"]);
    $scope->definePHPFunction("floor", [Formula::class,"floorFunc"]);
    $scope->definePHPFunction("round", [Formula::class,"roundFunc"]);
    $scope->definePHPFunction("sin", [Formula::class,"sinFunc"]);
    $scope->definePHPFunction("cos", [Formula::class,"cosFunc"]);
    $scope->definePHPFunction("tan", [Formula::class,"tanFunc"]);
    $scope->definePHPFunction("is_nan", [Formula::class,"is_nanFunc"]);
    $scope->definePHPFunction("abs", [Formula::class,"absFunc"]);
    $scope->definePHPFunction("asVector", [Formula::class,"asVectorFunc"]);
    $scope->definePHPFunction("sizeof", [Formula::class,"sizeofFunc"]);
    $scope->definePHPFunction("inRange", [Formula::class,"inRangeFunc"]);
    $scope->definePHPFunction("reduce", [Formula::class,"reduceFunc"]);
    $scope->definePHPFunction("firstOrNull", [Formula::class,"firstOrNullFunc"]);
    $scope->definePHPFunction("sum", [Formula::class,"sumFunc"]);
    return $scope;
  }

  private function buildDefaultScope(): Scope {
    $scope = new Scope();
    $this->parentScope->setParent(Formula::$inbuildScope);
    $scope->setParent($this->parentScope);
    return $scope;
  }

  public function getFormula(): string {
    return $this->expression->toString();
  }
}