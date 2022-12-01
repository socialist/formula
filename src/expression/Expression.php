<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\operator\Calculateable;

/**
 * 
 * @author Timo Lehnertz
 *
 */
interface Expression {
    /**
     * THis function will calculate the value of this Expression and all sub expressions if neccessary
     * @return float|string calculated value
     */
    public function calculate(): Calculateable;
}