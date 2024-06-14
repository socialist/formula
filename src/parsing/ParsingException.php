<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ParsingException extends \Exception {

  public const PARSING_ERROR_UNEXPECTED_END_OF_INPUT = 1;

  public const PARSING_ERROR_TOO_MANY_DELIMITERS = 2;

  public const PARSING_ERROR_MISSING_DELIMITERS = 3;

  public const PARSING_ERROR_INVALID_TYPE = 4;

  public const PARSING_ERROR_INVALID_OPERATOR_USE = 5;

  public const PARSING_ERROR_EXPECTED_SEMICOLON = 6;

  public const PARSING_ERROR_EXPECTED_EOF = 7;

  public const PARSING_ERROR_INCOMPLETE_TERNARY = 8;

  public const PARSING_ERROR_EXPECTED_CLOSING_CURLY_BRACKETS = 9;

  public const PARSING_ERROR_EXPECTED = 10;

  public const PARSING_ERROR_TOO_MANY_ELSE = 11;

  public readonly int $code;

  public readonly ?Token $token;

  public readonly ?string $additionalInfo;

  /**
   * @param ParsingException::PARSING_ERROR_* $code
   * @param ParsingException::LEVEL_* $level
   */
  public function __construct(int $code, ?Token $token, ?string $additionalInfo = null) {
    $this->code = $code;
    $this->token = $token;
    $this->additionalInfo = $additionalInfo;
    if($token !== null) {
      $message = 'Syntax error at: '.$token->line.':'.$token->position.' "'.$token->value.'" '.static::codeToMessage($code);
    } else {
      $message = 'Syntax error: '.static::codeToMessage($code);
    }
    if($additionalInfo !== null) {
      $message .= ' Message: '.$additionalInfo;
    }
    parent::__construct($message);
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
      case static::PARSING_ERROR_EXPECTED_EOF:
        return 'expected ond of file';
      case static::PARSING_ERROR_INCOMPLETE_TERNARY:
        return 'incomplete ternary expression';
      case static::PARSING_ERROR_EXPECTED_CLOSING_CURLY_BRACKETS:
        return 'expected closing curly brackets';
      case static::PARSING_ERROR_EXPECTED:
        return 'unexpected token';
      case static::PARSING_ERROR_TOO_MANY_ELSE:
        return 'else block cant follow else block';
      default:
        throw new \UnexpectedValueException($code.' is no valid ParsingExceptionCode');
    }
  }
}
