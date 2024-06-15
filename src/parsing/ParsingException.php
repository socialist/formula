<?php
declare(strict_types = 1);
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

  public const PARSING_ERROR_EXPECTED_EOF = 7;

  public const PARSING_ERROR_INCOMPLETE_TERNARY = 8;

  public const PARSING_ERROR_UNEXPECTED_TOKEN = 10;

  public const PARSING_ERROR_TOO_MANY_ELSE = 11;

  public readonly Parser $parser;

  public readonly int $parsingErrorCode;

  public readonly ?Token $token;

  public readonly ?string $additionalInfo;

  /**
   * @param ParsingException::PARSING_ERROR_* $code
   * @param ParsingException::LEVEL_* $level
   */
  public function __construct(Parser $parser, int $parsingErrorCode, ?Token $token = null, ?string $additionalInfo = null) {
    $this->parser = $parser;
    $this->parsingErrorCode = $parsingErrorCode;
    $this->token = $token;
    $this->additionalInfo = $additionalInfo;
    if($token !== null) {
      $message = 'Syntax error in '.$parser->name.': '.$token->line.':'.$token->position.' "'.($token->prev()?->value ?? '').' '.$token->value.' '.($token->next()?->value ?? '').'" Message: '.static::codeToMessage($parsingErrorCode);
    } else {
      $message = 'Syntax error in: '.$parser->name.': '.static::codeToMessage($parsingErrorCode);
    }
    if($additionalInfo !== null) {
      $message .= ' Message: '.$additionalInfo;
    }
    parent::__construct($message);
  }

  private static function codeToMessage(int $parsingErrorCode): string {
    switch($parsingErrorCode) {
      case static::PARSING_ERROR_UNEXPECTED_END_OF_INPUT:
        return 'Unexpected end of input';
      case static::PARSING_ERROR_TOO_MANY_DELIMITERS:
        return 'Too many delimiters';
      case static::PARSING_ERROR_MISSING_DELIMITERS:
        return 'Missing delimiter';
      case static::PARSING_ERROR_INVALID_TYPE:
        return 'Invalid type';
      case static::PARSING_ERROR_INVALID_OPERATOR_USE:
        return 'Invalid use of operator';
      case static::PARSING_ERROR_EXPECTED_EOF:
        return 'Expected ond of file';
      case static::PARSING_ERROR_INCOMPLETE_TERNARY:
        return 'Incomplete ternary expression';
      case static::PARSING_ERROR_UNEXPECTED_TOKEN:
        return 'Unexpected token';
      case static::PARSING_ERROR_TOO_MANY_ELSE:
        return 'Else block can\'t follow else block';
      default:
        throw new \UnexpectedValueException($parsingErrorCode.' is no valid ParsingErrorCode');
    }
  }
}
