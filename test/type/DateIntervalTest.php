<?php
namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\DateIntervalType;
use const true;

class DateIntervalTest extends TestCase {

  public function formatProvider(): array {
    // @formatter:off
    return [
      ["P0Y"],
      ["P1M"],
      ["P1D"],
      ["P1W"],
      ["PT1H"],
      ["PT1M"],
      ["PT1S"],

      ["P1Y"],
      ["P100Y"],
      ["P10000Y"],
      ["PT100000S"],

      ["P0Y1M2DT3H4M5S"],
      ["P0Y1M2WT3H4M5S"],
      ["P0Y1M2DT3H"],
      ["P0YT3H4M5S"],
      ["P0YT3H"],
      ["P0Y1M2D"],
      ["P0Y"],
    ];
    // @formatter:on
  }

  /**
   * @dataProvider formatProvider
   */
  public function testFormats(string $format): void {
    $formula = new Formula('"'.$format.'"');
    $this->assertInstanceOf(DateIntervalType::class, $formula->getReturnType());
    $this->assertEquals(new \DateInterval($format), $formula->calculate()->toPHPValue());
  }

  public function testDefine(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'interval', new \DateInterval("P1D"));
    $formula = new Formula('interval', $scope);
    $this->assertInstanceOf(DateIntervalType::class, $formula->getReturnType());
    $this->assertEquals(new \DateInterval('P1D'), $formula->calculate()->toPHPValue());
  }
}
