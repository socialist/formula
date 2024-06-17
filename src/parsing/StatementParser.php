<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

/**
 * @author Timo Lehnertz
 */
class StatementParser extends VariantParser {

  public function __construct() {
    // @formatter:off
    parent::__construct('statement', [
      new ExpressionStatementParser(),
      new CodeBlockParser(false, false),
      new VariableDeclarationStatementParser(),
      new ReturnStatementParser(),
      new WhileStatementParser(),
      new IfStatementParser(),
      new BreakStatementParser(),
      new ContinueStatementParser(),
      new DoWhileStatementParser(),
      new FunctionParser(true),
      new ForEachStatementParser(),
      new ForStatementParser(),
    ]);
    // @formatter:on
  }
}
