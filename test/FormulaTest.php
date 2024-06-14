<?php
namespace test;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\type\IntegerType;

class FormulaTest extends TestCase {

  //   public function testCodeBlock(): void {
  //     $formula = new Formula('int a = 0;a = a + 2;a = a+200/a;++a++;{return ++a++;}');
  //     $this->assertInstanceOf(IntegerType::class, $formula->getReturnType());
  //     $this->assertEquals(104, $formula->calculate()->toPHPValue());
  //   }

  //   public function testWhile(): void {
  //     $formula = new Formula('int a = 0; while(a<3){a++;} return a;');
  //     $this->assertInstanceOf(IntegerType::class, $formula->getReturnType());
  //     $this->assertEquals(3, $formula->calculate()->toPHPValue());
  //   }
  public function testIf(): void {
    $formula = new Formula('int a = 0; int b = 0; while(a<10){if(a % 2 == 0){b++;} a++ ;} return b;'); // count even numbers between 0 and 9
    $this->assertInstanceOf(IntegerType::class, $formula->getReturnType());
    $this->assertEquals(5, $formula->calculate()->toPHPValue());
  }

  //   public function testVariables(): void {
  //     $scope = new Scope();
  //     $scope->define('a', new IntegerType());
  //     $scope->define('b', new IntegerType());
  //     $scope->define('c', new IntegerType());
  //     $scope->define('d', new IntegerType());
  //     $scope->define('e', new FloatType());
  //     $str = 'a+b+c+d+e';
  //     $formula = new Formula($str, $scope);
  //     $this->assertInstanceOf(FloatType::class, $formula->getReturnType());
  //     for($i = 0;$i < 10;$i++) {
  //       $a = rand(-1000, 1000);
  //       $b = rand(-1000, 1000);
  //       $c = rand(-1000, 1000);
  //       $d = rand(-1000, 1000);
  //       $e = rand(-1000, 1000) + 1.5;
  //       $scope->assign('a', $a);
  //       $scope->assign('b', $b);
  //       $scope->assign('c', $c);
  //       $scope->assign('d', $d);
  //       $scope->assign('e', $e);
  //       $result = $formula->calculate();
  //       $this->assertInstanceOf(FloatValue::class, $result);
  //       $this->assertEquals($a + $b + $c + $d + $e, $result->getValue());
  //     }
  //   }

  //   public function testpow(): void {
  //     $str = 'pow(a,b)';
  //     $scope = new Scope();
  //     $scope->define('a', new IntegerType());
  //     $scope->define('b', new IntegerType());
  //     $formula = new Formula($str, $scope);
  //     for($i = 0;$i < 1;$i++) {
  //       $a = rand(0, 10);
  //       $b = rand(0, 10);
  //       $scope->assign('a', new IntegerValue($a));
  //       $scope->assign('b', new IntegerValue($b));
  //       $result = $formula->calculate();
  //       $this->assertEquals(pow($a, $b), $result->toPHPValue());
  //     }
  //   }

  //   public function testMathOperatorPrecedence(): void {
  //     $str = '(a+(b-c))*(a/d)*e+pow(a,b)*(b/d)-pow(a,e)';
  //     $scope = new Scope();
  //     $scope->define('a', new IntegerType());
  //     $scope->define('b', new IntegerType());
  //     $scope->define('c', new IntegerType());
  //     $scope->define('d', new IntegerType());
  //     $scope->define('e', new IntegerType());
  //     $formula = new Formula($str, $scope);
  //     for($i = 0;$i < 10;$i++) { // tested with 1000000
  //       $a = rand(1, 10);
  //       $b = rand(-10, 10);
  //       $c = rand(-10, 10);
  //       $d = rand(1, 10);
  //       $e = rand(1, 10);
  //       $scope->assign('a', $a);
  //       $scope->assign('b', $b);
  //       $scope->assign('c', $c);
  //       $scope->assign('d', $d);
  //       $scope->assign('e', $e);
  //       $correct = round(($a + ($b - $c)) * ($a / $d) * $e + pow($a, $b) * ($b / $d) - pow($a, $e));
  //       $calculated = $formula->calculate();
  //       $this->assertTrue(abs($calculated->toPHPValue() - $correct) < 1); // rounding errors...
  //     }
  //   }

  //   public function testFunctions(): void {
  //     $str = 'max(min(a,b),c)';
  //     $scope = new Scope();
  //     $scope->define('a', new IntegerType());
  //     $scope->define('b', new IntegerType());
  //     $scope->define('c', new IntegerType());
  //     $formula = new Formula($str, $scope);
  //     for($i = 0;$i < 10;$i++) {
  //       $a = rand(-1000, 1000);
  //       $b = rand(-1000, 1000);
  //       $c = rand(-1000, 1000);
  //       $scope->assign('a', $a);
  //       $scope->assign('b', $b);
  //       $scope->assign('c', $c);
  //       $this->assertEquals(max(min($a, $b), $c), $formula->calculate()->toPHPValue());
  //     }
  //   }

  //   public function testNesting(): void {
  //     $str = '((min(a,2)*b+5)/(((2+5)-5)+99*0))-7.5';
  //     $scope = new Scope();
  //     $scope->define('a', new IntegerType(), new IntegerValue(2));
  //     $scope->define('b', new IntegerType(), new IntegerValue(5));
  //     $formula = new Formula($str, $scope);
  //     $result = $formula->calculate();
  //     $this->assertEquals($result->toPHPValue(), 0);
  //   }

  //   //   /**
  //   //    * Dateintervals are not 100% precise
  //   //    */
  //   //   public function testDates(): void {
  //   //     $date = new DateTime(); // now
  //   //     $str = '"'.$date->format(DateTime::ISO8601).'" + "P5M" - "P1M" + "P1M" - "P5M"';
  //   //     $formula = new Formula($str);
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals($date->getTimestamp(), $result);

  //   //     $date = new DateTime(); // now
  //   //     $str = '"'.$date->format(DateTime::ISO8601).'" + 10 * "P1M" - 10 * "P1M"';
  //   //     $formula = new Formula($str);
  //   //     $result = $formula->calculate();
  //   //     $resDate = new DateTime();
  //   //     $resDate->setTimestamp($result);
  //   //     $this->assertEquals($date->getTimestamp(), intval($result));

  //   //     $date = new DateTime(); // now
  //   //     $str = '"'.$date->format(DateTime::ISO8601).'" - "'.$date->format(DateTime::ISO8601).'"';
  //   //     $formula = new Formula($str);
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals($result, 0);
  //   //   }
  //   public function testNotClosedBracket(): void {
  //     $this->expectException(ParsingException::class);
  //     $this->expectExceptionMessage('Parsing failed. Message: unexpected end of input');
  //     new Formula('(1*5');
  //   }

  //   public function booleanDataProvider() {
  //     return [[false,false],[false,true],[true,false],[true,true]];
  //   }

  //   /**
  //    * @dataProvider booleanDataProvider
  //    */
  //   public function testLogicalOperators($a, $b): void {
  //     $aStr = $a ? "true" : "false";
  //     $bStr = $b ? "true" : "false";
  //     $formula = new Formula("$aStr&&$bStr");
  //     $this->assertEquals($a && $b, $formula->calculate()->toPHPValue() == 0 ? false : true);

  //     $formula = new Formula("$aStr||$bStr");
  //     $this->assertEquals($a || $b, $formula->calculate()->toPHPValue() == 0 ? false : true);

  //     $formula = new Formula("$aStr==$bStr");
  //     $this->assertEquals($a == $b, $formula->calculate()->toPHPValue() == 0 ? false : true);

  //     $formula = new Formula("$aStr!=$bStr");
  //     $this->assertEquals($a != $b, $formula->calculate()->toPHPValue() == 0 ? false : true);

  //     $formula = new Formula("!$aStr");
  //     $this->assertEquals(!$a, $formula->calculate()->toPHPValue() == 0 ? false : true);
  //   }

  //   public function ternaryDataProvider(): array {
  //     $arr = [];
  //     for($i = 0;$i < 1;$i++) {
  //       $arr[] = [rand(-100, 100),rand(-100, 100),rand(-100, 100)];
  //     }
  //     return $arr;
  //   }

  //   /**
  //    * @dataProvider ternaryDataProvider
  //    */
  //   public function testTernary($a, $b, $c): void {
  //     $formula = new Formula("$a < 0 ? $b : $c");
  //     $this->assertEquals($formula->calculate()->toPHPValue(), $a < 0 ? $b : $c);
  //     $formula = new Formula("max($b, min($a < 0 ? $b : $c, $c))");
  //     $this->assertEquals($formula->calculate()->toPHPValue(), max($b, min($a < 0 ? $b : $c, $c)));
  //     //  testing Not operator
  //     $formula = new Formula("max($b, min(!($a < 0) ? $b : $c, $c))");
  //     $this->assertEquals($formula->calculate()->toPHPValue(), max($b, min(!($a < 0) ? $b : $c, $c)));
  //   }

  //   //   public function numberProvider(): array {
  //   //     return [[-1],[5],[20],[-6],[0]];
  //   //   }

  //   //   /**
  //   //    * @dataProvider numberProvider
  //   //    */
  //   //   public function testBuildInFuncs($a): void {
  //   //     $formula = new Formula('min(2*a, 0)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals($formula->calculate(), min(2 * $a, 0));

  //   //     $formula = new Formula('max(2a, 0)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals($formula->calculate(), max(2 * $a, 0));

  //   //     $formula = new Formula('sqrt(10+a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(sqrt(10 + $a)));

  //   //     $formula = new Formula('pow(a, a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(pow($a, $a)));

  //   //     $formula = new Formula('floor(a * 0.3)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals($formula->calculate(), floor($a * 0.3));

  //   //     $formula = new Formula('ceil(a * 0.3)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals($formula->calculate(), ceil($a * 0.3));

  //   //     $formula = new Formula('round(a * 0.3)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals($formula->calculate(), round($a * 0.3));

  //   //     $formula = new Formula('sin(a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(sin($a)));

  //   //     $formula = new Formula('cos(a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(cos($a)));

  //   //     $formula = new Formula('tan(a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(tan($a)));

  //   //     $formula = new Formula('tan(a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), round(tan($a)));

  //   //     $formula = new Formula('abs(a)');
  //   //     $formula->setVariable('a', $a);
  //   //     $this->assertEquals(round($formula->calculate()), abs($a));

  //   //     $formula = new Formula('asVector(1,2,3,4)[2]');
  //   //     $this->assertEquals(3, $formula->calculate());

  //   //     $formula = new Formula('sizeof({1,2,3,4,{true,false}},"Test",2,true)');
  //   //     $this->assertEquals(9, $formula->calculate());

  //   //     $formula = new Formula('inRange(1,2,3)');
  //   //     $this->assertEquals(0, $formula->calculate());
  //   //     $formula = new Formula('inRange(2,2,3)');
  //   //     $this->assertEquals(1, $formula->calculate());

  //   //     $formula = new Formula('reduce({1,2,4,5}, {1,3,5})');
  //   //     $this->assertEquals([1,5], $formula->calculate());

  //   //     $formula = new Formula('sum({1,2,true,false,{}})');
  //   //     $this->assertEquals(4, $formula->calculate());

  //   //     $formula = new Formula('firstOrNull({1,2,4,5})');
  //   //     $this->assertEquals(1, $formula->calculate());

  //   //     $formula = new Formula('firstOrNull({})');
  //   //     $this->assertEquals(null, $formula->calculate());
  //   //   }

  //   //   // from original repo at https://github.com/socialist/formula
  //   //   public function testAllResults() {
  //   //     $parser = new Formula('2 * 2.65');
  //   //     $this->assertEquals('5.3', $parser->calculate()->toPHPValue());

  //   //     $parser = new Formula('2 * 2.65 + 25');
  //   //     $this->assertEquals('30.3', $parser->calculate()->toPHPValue());

  //   //     $parser = new Formula('2 * 2.65 + 25 / 3');
  //   //     $this->assertEquals(round('13.63'), round($parser->calculate()->toPHPValue()));

  //   //     $parser = new Formula('2 + 3 * 2.65 + 25');
  //   //     $this->assertEquals('34.95', $parser->calculate()->toPHPValue());

  //   //     $parser = new Formula('2 + 3 * 2.65 + 25 - 26');
  //   //     $this->assertEqualsWithDelta('8.95', $parser->calculate()->toPHPValue(), 0.0001);

  //   //     $parser = new Formula('2 + 3 - 4 * 2.65 + 25 - 26');
  //   //     $this->assertEqualsWithDelta('-6.6', $parser->calculate()->toPHPValue(), 0.0001);

  //   //     $parser = new Formula('( 15 + 235 ) * 2.65');
  //   //     $this->assertEquals('662.5', $parser->calculate()->toPHPValue());

  //   //     $parser = new Formula('( 2 + ( 3 - 4 ) ) * 2.65 + 25 - 26');
  //   //     $this->assertEqualsWithDelta('1.65', $parser->calculate()->toPHPValue(), 0.0001);

  //   //     $parser = new Formula('( 2 + ( 3 - 4 ) ) * ( 2.65 + ( 25 - 26 ) )');
  //   //     $this->assertEquals('1.65', $parser->calculate()->toPHPValue());

  //   //     $parser = new Formula('( 2 + ( 3 * 235 - 4 ) ) + 25');
  //   //     $this->assertEquals('728', $parser->calculate()->toPHPValue());
  //   //   }

  //   //   public function testGetVariables() {
  //   //     $formula = new Formula('a + b + max(c, b ? d : e)');
  //   //     $this->assertEquals($formula->getVariables(), ['a', 'b', 'c', 'd', 'e']);
  //   //   }

  //   //   public function testVectors(): void {
  //   //   	$formula = new Formula('{1,2,3} + {1,2,3}');
  //   //   	$this->assertEquals($formula->calculate(), [2,4,6]);
  //   //   	$formula = new Formula('{1,2,3} + 5');
  //   //   	$this->assertEquals($formula->calculate(), [6,7,8]);

  //   //   	$formula = new Formula('{1,2,3} - {1,2,3}');
  //   //   	$this->assertEquals($formula->calculate(), [0,0,0]);
  //   //   	$formula = new Formula('{1,2,3} - 5');
  //   //   	$this->assertEquals($formula->calculate(), [-4,-3,-2]);

  //   //   	$formula = new Formula('{1,2,3} * {1,2,3}');
  //   //   	$this->assertEquals($formula->calculate(), [1,4,9]);
  //   //   	$formula = new Formula('{1,2,3} * 5');
  //   //   	$this->assertEquals($formula->calculate(), [5,10,15]);

  //   //   	$formula = new Formula('{1,2,3} / {1,2,3}');
  //   //   	$this->assertEquals($formula->calculate(), [1,1,1]);
  //   //   	$formula = new Formula('{10,15,20} / 5');
  //   //   	$this->assertEquals($formula->calculate(), [2,3,4]);

  //   //   	$formula = new Formula('max({-10,15,20})');
  //   //   	$this->assertEquals(20, $formula->calculate());

  //   //   	$formula = new Formula('min({-10,15,20})');
  //   //   	$this->assertEquals(-10, $formula->calculate());
  //   //   }

  //   //   public function testVectorsOffsets(): void {
  //   //     $formula = new Formula('{1,2,3}[0]');
  //   //     $this->assertEquals($formula->calculate(), 1);
  //   //     $formula = new Formula('{1,2,3}[max(a,b)]');
  //   //     $formula->setVariable('a', 2);
  //   //     $formula->setVariable('b', -1);
  //   //     $this->assertEquals(3, $formula->calculate());

  //   //     $formula = new Formula('{1,2,3}[0]');
  //   //     $this->assertEquals($formula->calculate(), 1);
  //   //     $formula = new Formula('4 + a[i]');
  //   //     $formula->setVariable('a', [0,1,2,3,4,5]);
  //   //     $formula->setVariable('i', 2);
  //   //     $this->assertEquals(6, $formula->calculate());

  //   //     $formula = new Formula('{1,2,3}[0]');
  //   //     $this->assertEquals($formula->calculate(), 1);
  //   //     $formula = new Formula('avg(a) + a[i]');
  //   //     $formula->setVariable('a', [0,1,2,3,4,5]);
  //   //     $formula->setVariable('i', 2);
  //   //     $this->assertEquals(4.5, $formula->calculate());
  //   //   }

  //   //   public function testVectorInvalidIndex(): void {
  //   //     $this->expectException(ExpressionNotFoundException::class);
  //   //     $this->expectExceptionMessage('123 Is no valid array index');
  //   //     $formula = new Formula('{1,2,3}["123"]');
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testVectoroutOfBounds1(): void {
  //   //     $this->expectException(\OutOfBoundsException::class);
  //   //     $this->expectExceptionMessage('3 not in range 0 - 3');
  //   //     $formula = new Formula('{1,2,3}[3]');
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testVectoroutOfBounds2(): void {
  //   //     $this->expectException(\OutOfBoundsException::class);
  //   //     $this->expectExceptionMessage('-1 not in range 0 - 3');
  //   //     $formula = new Formula('{1,2,3}[-1]');
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testUnexpectedEndOfInputException(): void {
  //   //     $this->expectException(ExpressionNotFoundException::class);
  //   //     $this->expectExceptionMessage('Unexpected end of input. Formula: "(1+2+3"  At position: 6');
  //   //     $formula = new Formula('(1+2+3');
  //   //     $formula->calculate();
  //   //   }

  //   //   public function strFunc() {
  //   //     return "Hallo welt";
  //   //   }

  //   //   public function testGetStringLiterals(): void {
  //   //     $formula = new Formula('strFunc("hallo", "welt", "hallo", "welt")');
  //   //     $formula->setMethod('strFunc', [$this, "strFunc"]);
  //   //     $this->assertEquals("Hallo welt", $formula->calculate());
  //   //     $this->assertEquals(['hallo', 'welt', 'hallo', 'welt'], $formula->getStringLiterals());
  //   //   }

  //   //   public function testRenameVariables(): void {
  //   //     $formula = new Formula('a+b+maxFunc(a,minFunc(a,b))');
  //   //     $formula->renameVariables('a', 'c');
  //   //     $formula->renameVariables('b', 'd');
  //   //     $formula->renameVariables('D', 'f'); // to check that it is case sensitive
  //   //     $formula->renameMethods('minFunc', 'min', false);
  //   //     $formula->renameMethods('maxFunc', 'max', false);
  //   //     $formula->setVariable('c', 10);
  //   //     $formula->setVariable('d', 20);
  //   //     $this->assertEquals(10+20+max(10,min(10, 20)), $formula->calculate());
  //   //   }

  //   //   public function testRenameVariablesCaseInsensitive(): void {
  //   //     $formula = new Formula('A+B+MAXFUNC(A,MINFUNC(a,b))');
  //   //     $formula->renameVariables('a', 'c', false);
  //   //     $formula->renameVariables('b', 'd', false);
  //   //     $formula->renameMethods('minFunc', 'min', false);
  //   //     $formula->renameMethods('maxFunc', 'max', false);
  //   //     $formula->setVariable('c', 10);
  //   //     $formula->setVariable('d', 20);
  //   //     $this->assertEquals(10+20+max(10,min(10, 20)), $formula->calculate());
  //   //   }

  //   //   public function testRenameStrings(): void {
  //   //     $formula = new Formula('"Hallo"');
  //   //     $formula->renameStrings('Hallo', 'Welt');
  //   //     $this->assertEquals('Welt', $formula->calculate());
  //   //   }

  //   //   public function testRenameMethods(): void {
  //   //     $formula = new Formula('abc(1,2)');
  //   //     $formula->renameMethods('abc', 'min');
  //   //     $this->assertEquals(1, $formula->calculate());
  //   //   }

  //   //   public function provideFormulaStrings(): array {
  //   //     return [
  //   //       ['1+1'],
  //   //       ['a + b'],
  //   //       ['max(50, 3, 100) + (a ? b : min(b,a))'],
  //   //       ['"P1D" + "P2D"'],
  //   //       ['{a,b,c}[2]'],
  //   //       ['(1((1)1)2)'],
  //   //       ['"Hallo welt"'],
  //   //       ['a+b+max(a,min(a,b))'],
  //   //       ['{1,2,a+max(a,b,c)} + {1,2,3}'],
  //   //       ['(a+(b-c))*(a/d)*e+pow(a,b)*(b/d)-pow(a,e)'],
  //   //       ['a&&b||c^d!=e>=f'],
  //   //     ];
  //   //   }

  //   //   /**
  //   //    * @dataProvider provideFormulaStrings
  //   //    */
  //   //   public function testGetFormula(string $formulaString): void {
  //   //     $formula1 = new Formula($formulaString);
  //   //     $formula1->setVariable('a', 0);
  //   //     $formula1->setVariable('b', 1);
  //   //     $formula1->setVariable('c', 2);
  //   //     $formula1->setVariable('d', 3);
  //   //     $formula1->setVariable('e', 4);
  //   //     $formula1->setVariable('f', 5);
  //   //     $parsedString = $formula1->getFormula();
  //   //     $formula2 = new Formula($parsedString);
  //   //     $formula2->setVariable('a', 0);
  //   //     $formula2->setVariable('b', 1);
  //   //     $formula2->setVariable('c', 2);
  //   //     $formula2->setVariable('d', 3);
  //   //     $formula2->setVariable('e', 4);
  //   //     $formula2->setVariable('f', 5);
  //   //     $formula2->calculate();
  //   //     $this->assertEquals($formula1->calculate(), $formula2->calculate());
  //   //   }

  //   //   public function methodTest(): int {
  //   //     return 123;
  //   //   }

  //   //   public function testMethofNoArgs(): void {
  //   //     $formula1 = new Formula('methodTest()');
  //   //     $formula1->setMethod('methodTest', [$this, 'methodTest']);

  //   //     $this->assertEquals(123, $formula1->calculate());
  //   //   }

  //   //   public function testStringConcatination(): void {
  //   //     $formula = new Formula('1.0 + "Hello" + " " + "world" + 1.0');
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals('1Hello world1', $result);

  //   //     $formula = new Formula('"S"');
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals('S', $result);
  //   //   }

  //   //   public function testDotVariables(): void {
  //   //     $formula = new Formula('self.id');
  //   //     $formula->setVariable('self.id', 123);
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals(123, $result);
  //   //   }

  //   //   public function dummyArrayFunc(int $index, array $arr) {
  //   //     return $arr[$index];
  //   //   }

  //   //   public function testdummyArrayFunc(): void {
  //   //     $formula = new Formula('dummyArrayFunc(2, {"S3", "S4", "S10"})');
  //   //     $formula->setMethod('dummyArrayFunc', [$this, 'dummyArrayFunc']);
  //   //     $result = $formula->calculate();
  //   //     $this->assertEquals('S10', $result);
  //   //   }

  //   //   public function testStringifyBrackets(): void {
  //   //     $testFormula = "((((getModuleComponentIndex()==1)))?(1):((getModuleComponentIndex()>1)?(s362/getMeasurementAtComponentIndex((getModuleComponentIndex()-1),{'s362','s363','s364','s365','s366'})):0))*100";
  //   //     $formula = new Formula($testFormula);
  //   //     $stringified = $formula->getFormula();
  //   //     $this->assertEquals("(getModuleComponentIndex()==1?1:getModuleComponentIndex()>1?s362/getMeasurementAtComponentIndex(getModuleComponentIndex()-1,{'s362','s363','s364','s365','s366'}):0)*100", $stringified);
  //   //   }

  //   //   public function testEmptyExpression(): void {
  //   //     $testFormula = "testFunc(())";
  //   //     $this->expectException(ExpressionNotFoundException::class);
  //   //     $this->expectExceptionMessage('Expression can\'t be empty. Formula: "testFunc(())"  At position: 3');
  //   //     new Formula($testFormula);
  //   //   }

  //   //   function getModuleComponentIndexFunc(): int {
  //   //     return -1;
  //   //   }

  //   //   public function testComplexTernary(): void {
  //   //     $formula = new Formula("((getModuleComponentIndex()>=1&&getModuleComponentIndex()<=2)?1:(getModuleComponentIndex()>2?2:-1))");
  //   //     $formula->setMethod('getModuleComponentIndex', [$this, 'getModuleComponentIndexFunc']);
  //   //     $res = $formula->calculate();
  //   //     $this->assertEquals(-1, $res);
  //   //   }
  //   public function testSumFunc(): void {
  //     $res = (new Formula("sum({1,{{{2}},2},4}, 5, {6,7+8+9})"))->calculate();
  //     $this->assertEquals(1 + 2 + 2 + 4 + 5 + 6 + 7 + 8 + 9, $res->toPHPValue());
  //     $this->expectException(FormulaValidationException::class);
  //     $this->expectExceptionMessage('No conversion from string to array<mixed,mixed>|float exists');
  //     $formula = new Formula("sum('Error')");
  //     echo (json_encode($formula->getNodeTree()));
  //     $res = $formula->calculate();
  //   }

  //   //   public function testUnusedVariable(): void {
  //   //     $formula = new Formula("a ? b : c");
  //   //     $formula->setVariable('a', true);
  //   //     $formula->setVariable('b', 123);
  //   //     $this->assertEquals(123, $formula->calculate());
  //   //     $formula->setVariable('a', false);
  //   //     $this->expectException(NoVariableValueException::class);
  //   //     $this->expectExceptionMessage("Can't calculate. Variable c has no value");
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testResetVariable(): void {
  //   //     $formula = new Formula("a + b");
  //   //     $formula->setVariable('a', 1);
  //   //     $formula->setVariable('b', 2);
  //   //     $this->assertEquals(3, $formula->calculate());
  //   //     $formula->resetVariable('b');
  //   //     $this->expectException(NoVariableValueException::class);
  //   //     $this->expectExceptionMessage("Can't calculate. Variable b has no value");
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testResetAllVariables(): void {
  //   //     $formula = new Formula("a + b");
  //   //     $formula->setVariable('a', 1);
  //   //     $formula->setVariable('b', 2);
  //   //     $this->assertEquals(3, $formula->calculate());
  //   //     $formula->resetAllVariables();
  //   //     $this->expectException(NoVariableValueException::class);
  //   //     $this->expectExceptionMessage("Can't calculate. Variable a has no value");
  //   //     $formula->calculate();
  //   //   }

  //   //   public function mockFunction(): int {
  //   //     return 1;
  //   //   }

  //   //   public function testResetMethod(): void {
  //   //     $formula = new Formula("min(1,2,3)");
  //   //     $formula->resetMethod('min');
  //   //     $this->assertEquals(1, $formula->calculate());
  //   //     $formula = new Formula("testFunc()");
  //   //     $formula->setMethod('testFunc', [$this, 'mockFunction']);
  //   //     $this->assertEquals(1, $formula->calculate());
  //   //     $formula->resetMethod('testFunc');
  //   //     $this->expectException(ExpressionNotFoundException::class);
  //   //     $this->expectExceptionMessage("No method provided for testFunc!");
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testResetAllMethods(): void {
  //   //     $formula = new Formula("testFunc1() + testFunc2()");
  //   //     $formula->setMethod('testFunc1', [$this, 'mockFunction']);
  //   //     $formula->setMethod('testFunc2', [$this, 'mockFunction']);
  //   //     $this->assertEquals(2, $formula->calculate());
  //   //     $formula->resetAllMethods();
  //   //     $this->expectException(ExpressionNotFoundException::class);
  //   //     $this->expectExceptionMessage("No method provided for testFunc1!");
  //   //     $formula->calculate();
  //   //   }

  //   //   public function testNoVariableValueException(): void {
  //   //     $formula = new Formula("a");
  //   //     try {
  //   //       $formula->calculate();
  //   //     } catch (NoVariableValueException $e) {
  //   //       $this->once();
  //   //       $this->assertEquals('a', $e->getMissingVariable());
  //   //     }
  //   //   }

  //   //   /**
  //   //    * @dataProvider booleanDataProvider
  //   //    */
  //   //   public function testOperatorOrder($a, $b): void {
  //   //     $formula = new Formula('a&&b==a||!b^a!=!b');
  //   //     $formula->setVariable('a', $a);
  //   //     $formula->setVariable('b', $b);
  //   //     $this->assertEquals($a&&$b==$a||!$b^$a!=!$b, $formula->calculate() == 1);
  //   //   }

  //   //   public function testNegativeNumber(): void {
  //   //     $formula = new Formula("-5");
  //   //     $this->assertEquals(-5, $formula->calculate());
  //   //     $formula = new Formula("-5 + 3");
  //   //     $this->assertEquals(-2, $formula->calculate());
  //   //     $formula = new Formula("-(5) + 3");
  //   //     $this->assertEquals(-2, $formula->calculate());
  //   //     $formula = new Formula("(-5) + 3");
  //   //     $this->assertEquals(-2, $formula->calculate());
  //   //     $formula = new Formula("-(5 + 3)");
  //   //     $this->assertEquals(-8, $formula->calculate());
  //   //     $formula = new Formula("-(5 + -3)");
  //   //     $this->assertEquals(-2, $formula->calculate());
  //   //     $formula = new Formula("-(5 * -3)");
  //   //     $this->assertEquals(15, $formula->calculate());
  //   //   }

  //   //   public function testNull(): void {
  //   //     $formula = new Formula("null");
  //   //     $this->assertEquals(null, $formula->calculate());
  //   //     $formula = new Formula("a");
  //   //     $formula->setVariable('a', null);
  //   //     $this->assertEquals(null, $formula->calculate());

  //   //     $formula = new Formula("getModuleComponentIndex()>=1&&getModuleComponentIndex()<=2?s1858*s1883/1000*24:getModuleComponentIndex()>2?s1858*s564/1000*24:null");
  //   //     $formula->setMethod('getModuleComponentIndex', function() {return -1;});
  //   //     $formula->setVariable('s1858', 1);
  //   //     $formula->setVariable('s1883', 1);
  //   //     $formula->setVariable('s1858', 1);
  //   //     $formula->setVariable('s564', 1);
  //   //     $this->assertEquals(null, $formula->calculate());
  //   //   }
}