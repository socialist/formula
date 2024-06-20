<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

/**
 * @author Timo Lehnertz
 */
enum OperatorType {

  case PrefixOperator;

  case InfixOperator;

  case PostfixOperator;
}
