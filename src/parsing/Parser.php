<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\ParsingException;
use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\tokens\Token;

/**
 * Superclass for all parsers
 *
 * @author Timo Lehnertz
 *        
 */
abstract class Parser {

  public static function parseRequired(Token &$token): mixed {
    $parsed = self::parse($token);
    if($parsed === null) {
      throw new ParsingException('Failed parsing required part', $token);
    }
    return $parsed;
  }

  public static function parse(Token &$token): ?mixed {
    $startToken = $token;
    try {
      $parsed = self::parsePart($token);
    } catch(UnexpectedEndOfInputException $e) {
      $parsed = null;
    }
    if($parsed === null) {
      $token = $startToken;
    }
    return $parsed;
  }
  
  protected static abstract function parsePart(Token &$token): ?mixed;
}

