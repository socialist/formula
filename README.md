# Formula
An open source PHP formula parser based on https://github.com/socialist/formula

### Features
- Variables
- User defined functions
- DateTime
- DateInterval

# Usage

```php
$formula = new Formula("10*a+func(b,5)");
$formula->setVariable("a", 1);
$formula->setVariable("b", 2);
$formula->setMethod("max", [$this, "func"]);
$result = formula->calculate();
```

**Table of Contents**

[TOCM]

[TOC]

## Operators
All standart php operators are supported. `+-*/` and all logical operators.
Operators respect the mathematical rules. The multiplication operator can be left out between distinct expressions.

## Ternary operator
Ternary operator is supported in the form of
```php
a ? b : c
```

## Truthy values
Everything that is not equal to 0 is considered a truthy value

## Methods
You can call methods inside of a formula string and give them a definition in the in the formula object. All user defined methodsshould return something. The returned values get parsed into either a number, or a string. 

## Strings
Strings are supported in the form of `"a string"` or `'a string'`.  Strings are all truthy by default and dont really serve any purpose other than beeing a parameter for functions.

## Dates
Formula supportes DateTimeImmutable and DateInterval. To define those use a string containing a Date or string Example:
- DateTime: `"2022-11-25T23:05:47+0100"`
- DateInterval: `P1M` (interval of one month)
All php date formats are supported. Check out this site for a list of DateTimeFOrmats: https://www.php.net/manual/en/class.datetime.php
Here's a list for DateIntervals: https://www.php.net/manual/en/class.dateinterval.php
Internally all dates and intervals get parsed to the UNIX timestamp for easier calculation. So if a date gets passed as parameter to a method the method will receive the timestamp and should also return a timestamp is thats what its purpose is.

## Pre defined methods
Those methods are predefined and ready to use in any formula script
- `min`
- `max`
- `sqrt`
- `pow`
- `floor`
- `ceil`
- `round`
- `sin`
- `cos`
- `tan`
- `is_nan`
- `abs`

All these are linked to their PHP counterpart and act just like php functions
