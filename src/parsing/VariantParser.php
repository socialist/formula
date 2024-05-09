<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\tokens\Token;

class VariantParser extends Parser {

  /**
   * @var array<Parser>
   */
  private readonly array $parsers;

  /**
   * @param array<Parser> $parsers
   */
  public function __construct(array $parsers) {
    $this->parsers = $parsers;
  }

  protected function parsePart(Token $firstToken): ParserReturn|int {
    /** @var Parser $parser */
    foreach($this->parsers as $parser) {
      $result = $parser->parse($firstToken);
      if(!is_int($result)) {
        return $result;
      }
    }
    return ParsingException::PARSING_ERROR_GENERIC;
  }
}

