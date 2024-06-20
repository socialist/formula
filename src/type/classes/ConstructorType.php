<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\type\functions\FunctionType;
use TimoLehnertz\formula\type\functions\OuterFunctionArgumentListType;

/**
 * @author Timo Lehnertz
 */
class ConstructorType extends FunctionType {

  public function __construct(OuterFunctionArgumentListType $arguments, ClassType $classType) {
    parent::__construct($arguments, $classType);
  }
}
