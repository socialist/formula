<?php
namespace test\type;

use PHPUnit\Framework\TestCase;
use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\type\DateTimeImmutableType;
use TimoLehnertz\formula\procedure\Scope;

class DateTimeImmutableTest extends TestCase {

  public function testOK(): void {
    $formula = new Formula('"2024-01-01"');
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable('2024-01-01'), $formula->calculate()->toPHPValue());
  }

  public function testCalculation(): void {
    $formula = new Formula('"2024-01-01" + "P1D"');
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable('2024-01-02'), $formula->calculate()->toPHPValue());

    $formula = new Formula('"2024-01-02" - "P1D"');
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable('2024-01-01'), $formula->calculate()->toPHPValue());
  }

  public function testTypedVar(): void {
    $formula = new Formula('DateTimeImmutable date = "2024-01-01"; DateInterval interval = "P1D"; return date+interval;');
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable('2024-01-02'), $formula->calculate()->toPHPValue());
  }

  public function testDefine(): void {
    $scope = new Scope();
    $scope->definePHP(true, 'date', new \DateTimeImmutable("2024-01-01"));
    $formula = new Formula('date', $scope);
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable('2024-01-01'), $formula->calculate()->toPHPValue());
  }

  public function formatProvider(): array {
    // @formatter:off
    return [
      ["2008-09-15T15:53:00"],
      ["2008-09-15"],
      ["2008-09-15"],
      ["2007-03-01T13:00:00Z"],
      ["2015-10-05T21:46:54+00:00"],
      ["2015-10-05T21:46:54Z"],
      ["2008-09-15 11:12:13"],
      ["2008-09-15 11:12"],
      ["2008-09"],
      ["1988-05-26T23:00:00.000Z"],
    ];
    // @formatter:on
  }

  /**
   * @dataProvider formatProvider
   */
  public function testFormats(string $format): void {
    $formula = new Formula('"'.$format.'"');
    $this->assertInstanceOf(DateTimeImmutableType::class, $formula->getReturnType());
    $this->assertEquals(new \DateTimeImmutable($format), $formula->calculate()->toPHPValue());
  }
}
