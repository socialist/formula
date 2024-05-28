<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ParsingException extends \Exception {

  public const PARSING_ERROR_GENERIC = 1;

  public const PARSING_ERROR_UNEXPECTED_END_OF_INPUT = 2;

  public const PARSING_ERROR_TOO_MANY_DELIMITERS = 3;

  public const PARSING_ERROR_MISSING_DELIMITERS = 4;

  public const PARSING_ERROR_INVALID_TYPE = 5;

  public const PARSING_ERROR_INVALID_OPERATOR_USE = 6;

  public const PARSING_ERROR_EXPECTED_SEMICOLON = 7;

  /**
   * @param ParsingException::PARSING_ERROR_* $code
   */
  public function __construct(int $code, ?Token $token) {
    if($token !== null) {
      parent::__construct('Parsing failed at "'.$token->value.'" at: '.$token->line.':'.$token->position.' Message: '.static::codeToMessage($code));
    } else {
      parent::__construct('Parsing failed. Message: '.static::codeToMessage($code));
    }
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
      case static::PARSING_ERROR_INVALID_TYPE:
        return 'invalid type';
      case static::PARSING_ERROR_INVALID_OPERATOR_USE:
        return 'invalid use of operator';
      case static::PARSING_ERROR_EXPECTED_SEMICOLON:
        return 'expected ;';
      default:
        throw new \UnexpectedValueException($code.' is no valid ParsingExceptionCode');
    }
  }
}
