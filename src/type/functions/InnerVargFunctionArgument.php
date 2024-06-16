<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 *
 *         Represents a varg function argument as seen from inside a function
 */
class InnerVargFunctionArgument extends FormulaPart {

  public readonly bool $final;

  public readonly Type $type;

  public readonly string $name;

  public function __construct(bool $final, Type $type, string $name) {
    parent::__construct();
    $this->final = $final;
    $this->type = $type;
    $this->name = $name;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    return ($this->final ? 'final ' : '').$this->type->getIdentifier().'... '.$this->name;
  }
}
