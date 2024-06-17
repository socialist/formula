<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\expression\OperatorExpression;

/**
 * @author Timo Lehnertz
 */
class CodeBlockOrExpression extends Statement {

  private CodeBlock|Expression $content;

  public function __construct(CodeBlock|Expression $content) {
    parent::__construct();
    $this->content = $content;
  }

  public function validateStatement(Scope $scope, ?Type $allowedReturnType = null): StatementReturnType {
    if($this->content instanceof CodeBlock) {
      return $this->content->validate($scope, $allowedReturnType);
    } else if($this->content instanceof Expression) {
      if($allowedReturnType !== null) {
        $implicitType = $this->content->validate($scope);
        $this->content = OperatorExpression::castExpression($this->content, $implicitType, $allowedReturnType, $scope, $this);
      }
      return new StatementReturnType($this->content->validate($scope), Frequency::ALWAYS, Frequency::ALWAYS);
    }
  }

  public function runStatement(Scope $scope): StatementReturn {
    if($this->content instanceof CodeBlock) {
      return $this->content->run($scope);
    } else if($this->content instanceof Expression) {
      return new StatementReturn($this->content->run($scope), false, false);
    }
  }

  public function toString(?PrettyPrintOptions $prettyPrintOptions): string {
    return $this->content->toString($prettyPrintOptions);
  }

  public function buildNode(Scope $scope): array {
    if($this->content instanceof Expression) {
      return $this->content->buildNode($scope);
    } else {
      throw new \BadMethodCallException('Code blocks dont support node trees');
    }
  }
}
