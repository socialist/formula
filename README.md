# formula
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
$parser->parse();
$parser->calculate(); // 8.95

```