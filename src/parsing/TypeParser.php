<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\FloatType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\StringType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\UnexpectedEndOfInputException;
use TimoLehnertz\formula\type\VoidType;

/**
 * ArrayDimension ::= [](<ArrayDimension>|<>)
 * SingleType ::= (<PrimitiveType>|<Identifier>)<ArrayDimension>|<>
 * CompoundType ::= <Type>|<Type>...
 * Type ::= <SingleType> | <CompoundType> | (<CompoundType> | <Type>)<ArrayDimension|<>>
 *
 * @author Timo Lehnertz
 *
 */
class TypeParser extends Parser {

  private readonly bool $allowFinal;

  private bool $final;

  public function __construct(bool $allowFinal) {
    parent::__construct('type');
    $this->allowFinal = $allowFinal;
  }

  protected function parsePart(Token $firstToken): ParserReturn {
    $this->final = false;
    $token = $firstToken;
    if($this->allowFinal && $token->id === Token::KEYWORD_FINAL) {
      $this->final = true;
      $token = $token->next();
      if($token === null) {
        throw new UnexpectedEndOfInputException();
      }
    }
    $inBrackets = false;
    if($token->id === Token::BRACKETS_OPEN) {
      $inBrackets = true;
      $token = $token->next();
      if($token === null) {
        throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
      }
    }
    $types = [];
    while($token !== null) {
      if($token->id === Token::BRACKETS_OPEN) {
        $parsed = (new TypeParser(false))->parse($token);
      } else {
        $parsed = $this->parseSingleType($token);
      }
      $token = $parsed->nextToken;
      if($token === null) {
        $types[] = $parsed->parsed;
        break;
      }
      $parsed = $this->parseArrayDimension($token, $parsed->parsed);
      $types[] = $parsed->parsed;
      $token = $parsed->nextToken;
      if($token === null) {
        break;
      }
      if($token->id !== Token::INTL_BACKSLASH) {
        break;
      } else {
        $token = $token->next();
      }
    }
    if(count($types) === 0) {
      throw new ParsingException(ParsingException::PARSING_ERROR_INVALID_TYPE, $firstToken);
    }
    $type = CompoundType::buildFromTypes($types, $this->final);
    if($type === null) {
      throw new ParsingException(ParsingException::PARSING_ERROR_INVALID_TYPE, $firstToken);
    }
    if($inBrackets) {
      if($token === null || $token->id !== Token::BRACKETS_CLOSED) {
        throw new ParsingSkippedException();
      }
      $token = $token->next();
      if($token->hasNext()) {
        $parsed = $this->parseArrayDimension($token, $type);
        $token = $parsed->nextToken;
        $type = $parsed->parsed;
      }
    }
    return new ParserReturn($type, $token);
  }

  private function parseArrayDimension(Token $firstToken, Type $type): ParserReturn {
    $arrayDimension = 0;
    $token = $firstToken;
    while($token !== null) {
      if($token->id === Token::SQUARE_BRACKETS_OPEN) {
        if(!$token->hasNext()) {
          throw new ParsingException(ParsingException::PARSING_ERROR_UNEXPECTED_END_OF_INPUT);
        }
        $token = $token->next();
        if($token->id !== Token::SQUARE_BRACKETS_CLOSED) {
          throw new ParsingSkippedException();
        }
        $arrayDimension++;
      } else {
        break;
      }
      $token = $token->next();
    }
    while($arrayDimension > 0) {
      $type = new ArrayType(new IntegerType($this->final), $type, $this->final);
      $arrayDimension--;
    }
    return new ParserReturn($type, $token);
  }

  private function parseSingleType(Token $firstToken): ParserReturn {
    $type = null;
    if($firstToken->id === Token::KEYWORD_BOOL) {
      $type = new BooleanType($this->final);
    } else if($firstToken->id === Token::KEYWORD_INT) {
      $type = new IntegerType($this->final);
    } else if($firstToken->id === Token::KEYWORD_FLOAT) {
      $type = new FloatType($this->final);
    } else if($firstToken->id === Token::KEYWORD_STRING) {
      $type = new StringType($this->final);
    } else if($firstToken->id === Token::KEYWORD_VOID) {
      $type = new VoidType();
    } else {
      throw new ParsingSkippedException($firstToken->value.$firstToken->id);
    }
    if(!$firstToken->hasNext()) {
      return new ParserReturn($type, $firstToken->next());
    }
    $type = static::parseArrayDimension($firstToken->next(), $type);
    return $type;
  }
}
