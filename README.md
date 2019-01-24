# formula
[![Latest Stable Version](https://img.shields.io/packagist/v/seregas/formula.svg)](https://packagist.org/packages/seregas/formula)
[![Build Status](https://travis-ci.com/socialist/formula.svg?branch=master)](https://github.com/socialist/formula)
[![Total Downloads](https://img.shields.io/packagist/dt/seregas/formula.svg)](https://packagist.org/packages/seregas/formula)

Simple mathematical expression parser

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