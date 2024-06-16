<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\type\functions;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\expression\Expression;
use TimoLehnertz\formula\type\Type;

/**
 * @author Timo Lehnertz
 *
 *         Represents a function argument as seen from inside a function
 */
class InnerFunctionArgument extends FormulaPart {

  public readonly bool $final;

  public readonly Type $type;

  public readonly string $name;

  public readonly ?Expression $defaultExpression;

  public function __construct(bool $final, Type $type, string $name, ?Expression $defaultExpression) {
    parent::__construct();
    $this->final = $final;
    $this->type = $type;
    $this->name = $name;
    $this->defaultExpression = $defaultExpression;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->defaultExpression === null) {
      return ($this->final ? 'final ' : '').$this->type->getIdentifier().' '.$this->name;
    } else {
      return ($this->final ? 'final ' : '').$this->type->getIdentifier().' '.$this->name.' = '.$this->defaultExpression->toString($prettyPrintOptions);
    }
  }

  public function isOptional(): bool {
    return $this->defaultExpression !== null;
  }
}
