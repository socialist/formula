<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class VariantParser extends Parser {

  /**
   * @var array<Parser>
   */
  private readonly array $parsers;

  /**
   * @param array<Parser> $parsers
   */
  public function __construct(string $name, array $parsers) {
    parent::__construct($name);
    $this->parsers = $parsers;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    /** @var Parser $parser */
    foreach($this->parsers as $parser) {
      try {
        return $parser->parse($firstToken);
      } catch(ParsingSkippedException $e) {} // try the next one
    }
    throw new ParsingSkippedException();
  }
}
