<?php

namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Calculateable;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;


class ArrayExpression implements Calculateable {
	
	/**
	 * @var Expression[]
	 */
	private array $elements = [];
	
	public function __construct(array $elements) {
	  $this->elements = $elements;
	}
	
// 	public static function fromArray($array): ArrayExpression {
// 	  $vector = new ArrayExpression();
// 		foreach ($array as $value) {
// 			$vector->elements[] = Method::calculateableFromValue($value);
// 		}
// 		return $vector;
// 	}
	
	public function isTruthy(): bool {
		return true;
	}
	
	public function add(Calculateable $summand): Calculateable {
		$resultVector = clone $this;
		if($summand instanceof ArrayExpression) {			
			if($this->size() != $summand->size()) throw new \Exception("Can only calculate with arrays of same length");
			for ($i = 0; $i < $this->size(); $i++) {
				$resultVector->elements[$i] = $this->elements[$i]->calculate()->add($summand->elements[$i]->calculate());
			}
		} else {
			foreach ($resultVector->elements as &$element) {
				$element = $element->calculate()->add($summand);
			}
		}
		return $resultVector;
	}

	public function subtract(Calculateable $difference): Calculateable {
		$resultVector = clone $this;
		if($difference instanceof ArrayExpression) {
			if($this->size() != $difference->size()) throw new \Exception("Can only calculate with arrays of same length");
			for ($i = 0; $i < $this->size(); $i++) {
				$resultVector->elements[$i] = $this->elements[$i]->calculate()->subtract($difference->elements[$i]->calculate());
			}
		} else {
			foreach ($resultVector->elements as &$element) {
				$element = $element->calculate()->subtract($difference);
			}
		}
		return $resultVector;
	}
	
	public function multiply(Calculateable $factor): Calculateable {
		$resultVector = clone $this;
		if($factor instanceof ArrayExpression) {
			if($this->size() != $factor->size()) throw new \Exception("Can only calculate with arrays of same length");
			for ($i = 0; $i < $this->size(); $i++) {
				$resultVector->elements[$i] = $this->elements[$i]->calculate()->multiply($factor->elements[$i]->calculate());
			}
		} else {
			foreach ($resultVector->elements as &$element) {
				$element = $element->calculate()->multiply($factor);
			}
		}
		return $resultVector;
	}
	
	public function divide(Calculateable $factor): Calculateable {
		$resultVector = clone $this;
		if($factor instanceof ArrayExpression) {
			if($this->size() != $factor->size()) throw new \Exception("Can only calculate with arrays of same length");
			for ($i = 0; $i < $this->size(); $i++) {
				$resultVector->elements[$i] = $this->elements[$i]->calculate()->divide($factor->elements[$i]->calculate());
			}
		} else {
			foreach ($resultVector->elements as &$element) {
				$element = $element->calculate()->divide($factor);
			}
		}
		return $resultVector;
	}
	
	public function calculate(): Calculateable {
		return $this;
	}

	public function getElement(int $index): Expression {
	  if($index < 0 || $index >= $this->size()) throw new \OutOfBoundsException("$index not in range 0 - {$this->size()}");
		return $this->elements[$index];
	}

	public function validate(bool $throwOnError, Scope $scope): bool {
		foreach ($this->elements as $element) {
	    if(!$element->validate($throwOnError, $scope)) return false;
		}
		return true;
	}
	
	public function size(): int {
		return sizeof($this->elements);
	}
	
	public function getElements(): array {
	  return $this->elements;
	}
	
	public function toString(): string {
	  $string = '{';
	  $delimiter = '';
	  foreach ($this->elements as $element) {
	    $string .= $delimiter.$element->toString();
	    $delimiter = ',';
	  }
	  return $string.'}';
	}
	
  public function getReturnType(): Type {
    if(sizeof($this->elements) === 0) return new EmptyArrayType();
  }

  public function run(): ReturnValue {
    $values = [];
    foreach ($this->elements as $element) {
      $values[] = $element->calculate()->getValue();
    }
    return $values;
  }
}