<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;

/**
 * @author Timo Lehnertz
 */
class ParsingException extends \Exception {

  public const ERROR_UNEXPECTED_END_OF_INPUT = 1;

  public const ERROR_TOO_MANY_DELIMITERS = 2;

  public const ERROR_MISSING_DELIMITERS = 3;

  public const ERROR_INVALID_TYPE = 4;

  public const ERROR_INVALID_OPERATOR_USE = 5;

  public const ERROR_EXPECTED_EOF = 7;

  public const ERROR_INCOMPLETE_TERNARY = 8;

  public const ERROR_UNEXPECTED_TOKEN = 10;

  public const ERROR_TOO_MANY_ELSE = 11;

  public const ERROR_VARG_NOT_LAST = 12;

  public readonly Parser $parser;

  public readonly int $parsingErrorCode;

  public readonly Token $token;

  public readonly ?string $additionalInfo;

  private static Parser $currentParser;

  private static Token $currentToken;

  /**
   * @param ParsingException::ERROR_* $parsingErrorCode
   */
  public function __construct(int $parsingErrorCode, ?Token $token = null, ?string $additionalInfo = null) {
    $this->parsingErrorCode = $parsingErrorCode;
    $this->token = $token ?? ParsingException::$currentToken;
    $this->additionalInfo = $additionalInfo;
    $this->parser = ParsingException::$currentParser;
    $message = 'Syntax error in '.$this->parser->name.': '.($this->token->line + 1).':'.($this->token->position + 1).' '.$this->token->value.' . Message: '.static::codeToMessage($parsingErrorCode);
    if($additionalInfo !== null) {
      $message .= '. '.$additionalInfo;
    }
    parent::__construct($message);
  }

  public static function setParser(Parser $currentParser, Token $currentToken) {
    ParsingException::$currentParser = $currentParser;
    ParsingException::$currentToken = $currentToken;
  }

  private static function codeToMessage(int $parsingErrorCode): string {
    switch($parsingErrorCode) {
      case static::ERROR_UNEXPECTED_END_OF_INPUT:
        return 'Unexpected end of input';
      case static::ERROR_TOO_MANY_DELIMITERS:
        return 'Too many delimiters';
      case static::ERROR_MISSING_DELIMITERS:
        return 'Missing delimiter';
      case static::ERROR_INVALID_TYPE:
        return 'Invalid type';
      case static::ERROR_INVALID_OPERATOR_USE:
        return 'Invalid use of operator';
      case static::ERROR_EXPECTED_EOF:
        return 'Expected ond of file';
      case static::ERROR_INCOMPLETE_TERNARY:
        return 'Incomplete ternary expression';
      case static::ERROR_UNEXPECTED_TOKEN:
        return 'Unexpected token';
      case static::ERROR_TOO_MANY_ELSE:
        return 'Else block can\'t follow else block';
      case static::ERROR_VARG_NOT_LAST:
        return 'Varg argument must be last';
      default:
        throw new \UnexpectedValueException($parsingErrorCode.' is no valid ParsingErrorCode');
    }
  }
}
