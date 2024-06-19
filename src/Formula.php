<?php
namespace TimoLehnertz\formula;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;
use TimoLehnertz\formula\nodes\NodeTree;
use TimoLehnertz\formula\parsing\CodeBlockOrExpressionParser;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\statement\CodeBlockOrExpression;
use TimoLehnertz\formula\tokens\TokenisationException;
use TimoLehnertz\formula\tokens\Tokenizer;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidType;
use TimoLehnertz\formula\type\VoidValue;
use TimoLehnertz\formula\procedure\DefaultScope;

/**
 * This class represents a formula session that can interpret/run code
 *
 * @author Timo Lehnertz
 */
class Formula {

  private readonly CodeBlockOrExpression $content;

  private DefaultScope $defaultScope;

  private readonly Scope $parentScope;

  private readonly FormulaSettings $formulaSettings;

  private readonly string $source;

  private readonly Type $returnType;

  public function __construct(string $source, ?Scope $parentScope = null, ?FormulaSettings $formulaSettings = null) {
    $this->source = $source;
    $this->defaultScope = new DefaultScope();
    if($formulaSettings === null) {
      $formulaSettings = FormulaSettings::buildDefaultSettings();
    }
    $this->formulaSettings = $formulaSettings;
    $this->parentScope = $parentScope ?? new Scope();
    $firstToken = Tokenizer::tokenize($source);
    if($firstToken !== null) {
      $firstToken = $firstToken->skipComment();
    }
    if($firstToken === null) {
      throw new TokenisationException('Invalid formula', 0, 0);
    }
    $parsedContent = (new CodeBlockOrExpressionParser(true))->parse($firstToken, true, true);
    $this->content = $parsedContent->parsed;
    $this->returnType = $this->content->validate($this->buildScope())->returnType ?? new VoidType();
  }

  /**
   * @throws NodesNotSupportedException
   */
  public function getNodeTree(): NodeTree {
    $node = $this->content->buildNode($this->buildScope());
    return new NodeTree($node, $this->buildScope()->toNodeTreeScope());
  }

  public function getReturnType(): Type {
    return $this->returnType;
  }

  /**
   * Calculates and returnes the result of this formula
   */
  public function calculate(): Value {
    return $this->content->run($this->buildScope())->returnValue ?? new VoidValue();
  }

  private function buildScope(): Scope {
    $scope = new Scope();
    $this->parentScope->setParent($this->defaultScope);
    $scope->setParent($this->parentScope);
    return $scope;
  }

  public function prettyprintFormula(?PrettyPrintOptions $prettyprintOptions = null): string {
    if($prettyprintOptions === null) {
      $prettyprintOptions = PrettyPrintOptions::buildDefault();
    }
    return $this->content->toString($prettyprintOptions);
  }
}