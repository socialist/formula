<?php
namespace src\operator;

/**
 *
 * @author Timo Lehnertz
 *        
 */
enum OperatorType {

  case Prefix;

  case Infix;

  case InfixCommutative;

  case Postfix;
}

