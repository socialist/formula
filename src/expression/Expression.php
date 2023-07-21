<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;
use TimoLehnertz\formula\procedure\ReturnValue;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\types\Type;

/**
 * 
 * @author Timo Lehnertz
 *
 */
interface Expression extends FormulaPart {
    
    public function validate(Scope $scope): Type;
    
    public function run(): ReturnValue;
    
    /**
     * Converts this expression back to code
     * @return string
     */
    public function toString(): string;
}

