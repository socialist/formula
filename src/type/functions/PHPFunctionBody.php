<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\VoidValue;

/**
 * @author Timo Lehnertz
 */
class PHPFunctionBody implements FunctionBody {

  /**
   * @var callable
   */
  private readonly mixed $callable;

  /**
   * PHP void Functions always return null
   */
  private readonly bool $voidFunction;

  public function __construct(callable $callable, bool $voidFunction) {
    $this->callable = $callable;
    $this->voidFunction = $voidFunction;
  }

  public function call(OuterFunctionArgumentListValue $argList): Value {
    $args = [];
    for($i = 0;$i < count($argList->getValues());$i++) {
      /** @var Value $argValue */
      $argValue = $argList->getValues()[$i];
      $args[$i] = $argValue->toPHPValue();
    }
    $phpReturn = call_user_func_array($this->callable, $args);
    if(!$this->voidFunction) {
      return Scope::convertPHPVar($phpReturn, true)[1];
    } else {
      return new VoidValue();
    }
  }
}
