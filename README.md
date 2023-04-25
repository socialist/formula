# formula
[![Latest Stable Version](https://img.shields.io/packagist/v/seregas/formula.svg)](https://packagist.org/packages/seregas/formula)
[![Build Status](https://travis-ci.com/socialist/formula.svg?branch=master)](https://github.com/socialist/formula)
[![Total Downloads](https://img.shields.io/packagist/dt/seregas/formula.svg)](https://packagist.org/packages/seregas/formula)

This package can parse and evaluate formulas with variables.

It can take a string with a math expression and parses it so it can be evaluated replacing variables in the expression by given values.

The packages supports operations like addition, subtraction, multiplication and division.

Changes in comparison to original project
-----------------------------------------

- allow multi-character variable names
- allow reuse of formula object:
```php
$formula = new Formula('a + b');
$formula->setVariable('a', 1);
$formula->setVariable('b', 41);
$c = $formula->calculate();     // 42
$formula->setVariable('a', 3);
$formula->setVariable('b', 1334);
$d = $formula->calculate();     // 1337    
```
- remove try-catch to hide exceptions from stdout (e.g. website)
- bugfix for formulas that use brackets like `(a + b) * c`
- remove `round(..., 2)` for divisions to avoid minor calculation errors
- changed division-by-zero behavior from PHP's error to giving result `NAN` (a PHP constant) because I prefer silent error handling in this case ;-)
- added exception if a variable is undefined (missing `setVariable(...)` statement)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist seregas/formula "*"
```

or add

```
"seregas/formula": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
$parser = new \socialist\formula\Formula('2 + 3 * 2,65 + 25 - 26');
$parser->calculate(); // 8.95

```

Also in the formula you can use variables:

```php

$parser = new \socialist\formula\Formula('2 + 3 * p + 25 - 26');
$parser->setVariable('p', 2,65);
$parser->calculate(); // 8.95

```

And insert comments like `/*...*/`, `[...]` or `{...}`:

```php

$parser = new \socialist\formula\Formula('2 + 3 * p /* price */ + 25 - 26');
$parser->setVariable('p', 2,65);
$parser->calculate(); // 8.95

```