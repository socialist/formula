<?php
namespace TimoLehnertz\formula;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 *
 */
class ParsingException extends \Exception {

  public const PARSING_ERROR_GENERIC = 1;

  public const PARSING_ERROR_UNEXPECTED_END_OF_INPUT = 2;

  public const PARSING_ERROR_TOO_MANY_DELIMITERS = 3;

  public const PARSING_ERROR_MISSING_DELIMITERS = 4;

  public function __construct(int $code, Token $token) {
    parent::__construct('Unexpected token "'.$token->value.'" at: '.$token->line.':'.$token->position.' Message: '.static::codeToMessage($code));
  }

  private static function codeToMessage(int $code): string {
    switch($code) {
      case static::PARSING_ERROR_GENERIC:
        return 'parsing failed';
      case static::PARSING_ERROR_UNEXPECTED_END_OF_INPUT:
        return 'unexpected end of input';
      case static::PARSING_ERROR_TOO_MANY_DELIMITERS:
        return 'too many delimiters';
      case static::PARSING_ERROR_MISSING_DELIMITERS:
        return 'missing delimiter';
      default:
        throw new \UnexpectedValueException($code.' is no valid ParsingExceptionCode');
    }
  }
}