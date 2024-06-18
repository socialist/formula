<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\classes;

use TimoLehnertz\formula\type\functions\FunctionBody;
use TimoLehnertz\formula\type\functions\FunctionValue;

/**
 * @author Timo Lehnertz
 */
class ConstructorValue extends FunctionValue {

  public function __construct(FunctionBody $body) {
    parent::__construct($body);
  }
}
