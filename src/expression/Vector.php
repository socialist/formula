<?php

namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\ExpressionNotFoundException;
use TimoLehnertz\formula\Nestable;
use TimoLehnertz\formula\Parseable;
use TimoLehnertz\formula\operator\Calculateable;

class Vector implements Calculateable, Nestable, Parseable {
	
	/**
	 * @var array<Expression>
	 */
	private array $elements = [];
	
	public static function fromArray($array): Vector {
		$vector = new Vector();
		foreach ($array as $value) {
			$vector->elements[] = Method::calculateableFromValue($value);
		}
		return $vector;
	}
	
	public function isTruthy(): bool {
		return true;
	}
	
	public function add(Calculateable $summand): Calculateable {
		$resultVector = clone $this;
		if($summand instanceof Vector) {			
			if($this->size() != $summand->size()) throw new \Exception("Can only calculate with vectors of same length");
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
		if($difference instanceof Vector) {
			if($this->size() != $difference->size()) throw new \Exception("Can only calculate with vectors of same length");
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
		if($factor instanceof Vector) {
			if($this->size() != $factor->size()) throw new \Exception("Can only calculate with vectors of same length");
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
		if($factor instanceof Vector) {
			if($this->size() != $factor->size()) throw new \Exception("Can only calculate with vectors of same length");
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
	
	public function getValue(): array {
		$values = [];
		foreach ($this->elements as $element) {
			$values[] = $element->calculate()->getValue();
		}
		return $values;
	}
	
	public function pow(Calculateable $power): Calculateable {
		// not used
	}
	
	public function calculate(): Calculateable {
		return $this;
	}

	public function getElement(int $index): Expression {
	  if($index < 0 || $index >= $this->size()) throw new \OutOfBoundsException("$index not in range 0 - {$this->size()}");
		return $this->elements[$index];
	}
	
	public function setMethod(string $identifier, callable $method): void {
		foreach ($this->elements as $element) {
			if($element instanceof Nestable) {
				$element->setMethod($identifier, $method);
			}
		}
	}

	public function getVariables(): array {
		$variables = [];
		foreach ($this->elements as $element) {
			if($element instanceof Nestable) {
				foreach ($element->getVariables() as $variable) {
					$variables[] = $variable;
				}
			}
		}
		return $variables;
	}

	public function setVariable(string $identifier, $value): void {
		foreach ($this->elements as $element) {
			if($element instanceof Nestable) {
				$element->setVariable($identifier, $value);
			}
		}
	}

	public function validate(bool $throwOnError): bool {
		foreach ($this->elements as $element) {
			if(!$element->validate($throwOnError)) return false;
		}
		return true;
	}
	
	public function parse(array &$tokens, int &$index): bool {
		if($tokens[$index]->value != '{') return false;
	  if(sizeof($tokens) < $index + 2) throw new ExpressionNotFoundException("Invalid Vector");
		$index++; // skipping [
		$first = true;
		for (; $index < sizeof($tokens); $index++) {
			$token = $tokens[$index];
			if($token->value == '}') {
				$index++;
				return true;
			}
			if($token->value == ',') {
				$index++;
				if(sizeof($tokens) < $index + 1 || $first) throw new ExpressionNotFoundException("Invalid Vector");;
			}
			$expression = new MathExpression();
			$expression->parse($tokens, $index); // will throw on error
			if($expression->size() == 0) throw new ExpressionNotFoundException("Invalid element");
			$this->elements[] = $expression;
			$token = $tokens[$index];
			$index--; // go back one to avoid increment
			$first = false;
		}
		throw new ExpressionNotFoundException("Invalid Vector: Unexpected end of input. Please add ]");
	}
	
	public function size(): int {
		return sizeof($this->elements);
	}
}