<?php
namespace TimoLehnertz\formula\parsing;

use TimoLehnertz\formula\type\ArrayType;
use TimoLehnertz\formula\type\BooleanType;
use TimoLehnertz\formula\type\CompoundType;
use TimoLehnertz\formula\type\IntegerType;
use TimoLehnertz\formula\type\ReferenceType;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\tokens\Token;
use TimoLehnertz\formula\type\FloatType;

/**
 * ArrayDimension ::= [](<ArrayDimension>|<>)
 * SingleType ::= (<PrimitiveType>|<Identifier>)<ArrayDimension>|<>
 * CompoundType ::= <Type>|<Type>...
 * Type ::= <SingleType> | <CompoundType> | (<CompoundType> | <Type>)<ArrayDimension|<>>
 *
 * @author Timo Lehnertz
 *        
 */
class TypeParser {

  /**
   *
   * @param Token[] $tokens
   */
  private static function parseArrayDimension(array &$tokens, int &$index, Type $type): ?Type {
    $startIndex = $index;
    $arrayDimension = 0;
    do {
      if($index >= sizeof($tokens)) {
        break;
      }
      $token = $tokens[$index];
      if($token->name === '[') {
        $index++;
        if($index >= sizeof($tokens)) {
          $index = $startIndex;
          return null;
        }
        $token = $tokens[$index];
        if($token->name !== ']') {
          return null;
        }
        $index++;
        $arrayDimension++;
      } else {
        break;
      }
    } while(true);
    if($arrayDimension !== 0) {
      $type = new ArrayType(new IntegerType(), $type);
      for($i = 0;$i < $arrayDimension - 1;$i++) {
        $type = new ArrayType(new IntegerType(), $type);
      }
    }
    return $type;
  }

  /**
   *
   * @param Token[] $tokens
   */
  private static function parseSingleType(array &$tokens, int &$index): ?Type {
    $token = $tokens[$index];
    $type = null;
    $startIndex = $index;
    if($token->name === 'I') {
      $type = new ReferenceType($token->value);
    } else if($token->name === 'bool') {
      $type = new BooleanType();
    } else if($token->name === 'int') {
      $type = new IntegerType();
    } else if($token->name === 'float') {
      $type = new FloatType();
    } else {
      return null;
    }
    $index++;
    if($index >= sizeof($tokens)) {
      return $type;
    }
    $type = static::parseArrayDimension($tokens, $index, $type);
    if($type === null) {
      $index = $startIndex;
      return null;
    }
    return $type;
  }

  /**
   *
   * @param Token[] $tokens
   */
  public static function parseType(array &$tokens, int &$index): ?Type {
    $inBrackets = false;
    $startIndex = $index;
    $token = $tokens[$index];
    if($token->name === '(') {
      $inBrackets = true;
      $index++;
      if($index >= sizeof($tokens)) {
        $index = $startIndex;
        return null;
      }
    }
    $types = [];
    do {
      $token = $tokens[$index];
      if($token->name === '(') {
        $type = static::parseType($tokens, $index);
      } else {
        $type = static::parseSingleType($tokens, $index);
      }
      if($type === null) {
        break;
      }
      $type = static::parseArrayDimension($tokens, $index, $type);
      if($type === null) {
        $index = $startIndex;
        return null;
      }
      $types[] = $type;
      if($index >= sizeof($tokens)) {
        break;
      }
      $token = $tokens[$index];
      if($token->name !== '|') {
        break;
      } else {
        $index++;
      }
      if($index >= sizeof($tokens)) {
        $index = $startIndex;
        return null;
      }
    } while(true);
    $type = CompoundType::concatManyTypes($types);
    if($type === null) {
      $index = $startIndex;
      return $type;
    }
    if($index >= sizeof($tokens)) {
      return $type;
    }
    $token = $tokens[$index];
    if($inBrackets) {
      if($token->name !== ')') {
        $index = $startIndex;
        return null;
      }
      $index++;
      if($index > sizeof($tokens)) {
        $index = $startIndex;
        return null;
      }
      $type = static::parseArrayDimension($tokens, $index, $type);
      if($type === null) {
        $index = $startIndex;
        return null;
      }
    }
    return $type;
  }
}
