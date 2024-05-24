<?php
declare(strict_types = 1);
namespace TimoLehnertz\formula\operator;

use TimoLehnertz\formula\PrettyPrintOptions;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Type;
use TimoLehnertz\formula\type\TypeValue;
use TimoLehnertz\formula\type\Value;
use TimoLehnertz\formula\type\TypeType;

/**
 * @author Timo Lehnertz
 */
class TypeCastOperator extends PrefixOperator {

  private readonly bool $explicit;

  private readonly Type $initialType;

  private ?Type $validatedType = null;

  public function __construct(bool $explicit, Type $type) {
    parent::__construct(3);
    $this->explicit = $explicit;
    $this->initialType = $type;
  }

  public function validate(Scope $scope): void {
    $this->validatedType = $this->initialType->validate($scope);
  }

  protected function validatePrefixOperation(Type $rightType): Type {
    if($this->validatedType === null) {
      throw new \BadFunctionCallException('Must call validate() first!');
    }
    $type = $rightType->getOperatorResultType($this, new TypeType($this->validatedType));
    if(!$this->validatedType->equals($type)) {
      throw new \UnexpectedValueException('Type cast operation returned unexpected type. Expected '.$this->validatedType->getIdentifier().' Got: '.$type->getIdentifier());
    }
    return $type;
  }

  protected function operatePrefix(Value $rightValue): Value {
    if($this->validatedType === null) {
      throw new \BadFunctionCallException('Must call validate() first!');
    }
    $castedValue = $rightValue->operate($this, new TypeValue($this->validatedType));
    if(!$this->validatedType->equals($castedValue->getType())) {
      throw new \UnexpectedValueException('Type cast operation returned unexpected type. Expected '.$this->validatedType->getIdentifier().' Got: '.$castedValue->getType()->getIdentifier());
    }
    return $castedValue;
  }

  public function getCastType(): Type {
    if($this->validatedType === null) {
      throw new \BadFunctionCallException('Must call validate() first!');
    }
    return $this->validatedType;
  }

  public function toString(PrettyPrintOptions $prettyPrintOptions): string {
    if($this->explicit) {
      return '('.$this->initialType->getIdentifier().')';
    } else {
      return '';
    }
  }
}

