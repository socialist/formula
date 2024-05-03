<?php
namespace TimoLehnertz\formula\expression;

use TimoLehnertz\formula\FormulaPart;

/**
 * An Expression is a executable piece of code that can exist anywhere.
 * Specifically right to an equal sign if its returntype is non void.
 * Expressions don't define anything in their scopes
 *
 * @author Timo Lehnertz
 */
interface Expression extends FormulaPart {}

