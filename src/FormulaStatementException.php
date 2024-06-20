<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\statement\Statement;

/**
 * @author Timo Lehnertz
 */
class FormulaStatementException extends \Exception {

  private static ?Statement $currentStatement = null;

  public function __construct(string $message) {
    if(FormulaStatementException::$currentStatement !== null && FormulaStatementException::$currentStatement->firstToken !== null) {
      $token = FormulaStatementException::$currentStatement->firstToken;
      parent::__construct(($token->line + 1).':'.$token->position.' '.$message);
    } else {
      parent::__construct($message);
    }
  }

  public static function setCurrentStatement(Statement $currentStatement): void {
    FormulaStatementException::$currentStatement = $currentStatement;
  }
}
