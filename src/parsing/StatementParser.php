<?php
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
      new ForStatementParser(),
      new ContinueStatementParser(),
      new DoWhileStatementParser(),
      new FunctionParser(true),
    ]);
    // @formatter:on
  }
}
