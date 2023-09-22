<?php
namespace TimoLehnertz\formula\procedure;

use TimoLehnertz\formula\type\Locator;

/**
 * Class readonly containing information about the termination of a statement
 * Will get passed back from the run method of Statements
 * 
 * @author Timo Lehnertz
 *
 */
class StatementReturnInfo {
  
  private function __construct() {
    
  }
  
  /**
   * Null if no ReturnStatement was called
   * Locator íf a ReturnStatement was called containing the returned value
   */
  private ?Locator $returnLocator;
  
  /**
   * Null if no continue statement was called
   * int the number of continued cycles otherwise
   */
  private ?int $continueCount = null;
  
  /**
   * Indicates wether a break statement was executd
   */
  private bool $breakFlag = false;
  
  public static function buildReturn(Locator $locator): StatementReturnInfo {
    $statementReturnInfo = new StatementReturnInfo();
    $statementReturnInfo->returnLocator = $locator;
    return $statementReturnInfo;
  }
  
  public static function buildContinue(int $continueCount): StatementReturnInfo {
    $statementReturnInfo = new StatementReturnInfo();
    $statementReturnInfo->continueCount = $continueCount;
    return $statementReturnInfo;
  }
  
  public static function buildBreak(): StatementReturnInfo {
    $statementReturnInfo = new StatementReturnInfo();
    $statementReturnInfo->breakFlag = true;
    return $statementReturnInfo;
  }

  public static function buildBoring(): StatementReturnInfo {
    return new StatementReturnInfo();;
  }
}

