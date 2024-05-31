<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\statement;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\procedure\Scope;

/**
 * @author Timo Lehnertz
 */
class CodeBlockOrExpression implements Statement {

  private readonly CodeBlock|Expression $content;

  public function __construct(CodeBlock|Expression $content) {
    $this->content = $content;
  }

  public function validate(Scope $scope): StatementReturnType {
    if($this->content instanceof CodeBlock) {
      return $this->content->validate($scope);
    } else if($this->content instanceof Expression) {
      return new StatementReturnType($this->content->validate($scope), false, true);
    }
  }

  public function run(Scope $scope): StatementReturn {
    if($this->content instanceof CodeBlock) {
      return $this->content->run($scope);
    } else if($this->content instanceof Expression) {
      return new StatementReturn($this->content->run($scope), true, false, 0);
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
