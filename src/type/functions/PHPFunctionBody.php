<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\Value;

/**
 * @author Timo Lehnertz
 */
class PHPFunctionBody implements FunctionBody {

  /**
   * @var callable
   */
  private readonly mixed $callable;

  private readonly Type $returnType;

  public function __construct(callable $callable, Type $returnType) {
    $this->callable = $callable;
    $this->returnType = $returnType;
  }

  public function call(OuterFunctionArgumentListValue $argList): Value {
    $args = [];
    for($i = 0;$i < count($argList->getValues());$i++) {
      /** @var Value $argValue */
      $argValue = $argList->getValues()[$i];
      $args[$i] = $argValue->toPHPValue();
    }
    $phpReturn = call_user_func_array($this->callable, $args);
    return Scope::valueByPHPVar($phpReturn);
  }
}
