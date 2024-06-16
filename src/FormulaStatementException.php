<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\statement\Statement;

/**
 * @author Timo Lehnertz
 */
class FormulaStatementException extends \Exception {

  private static Statement $currentStatement;

  public function __construct(string $message) {
    $token = FormulaStatementException::$currentStatement->firstToken;
    parent::__construct($token->line.':'.$token->position.''.$message);
  }

  public static function setCurrentStatement(Statement $currentStatement): void {
    FormulaStatementException::$currentStatement = $currentStatement;
  }
}
