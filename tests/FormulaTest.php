<?php

use PHPUnit\Framework\TestCase;
use socialist\formula\Formula;

class FormulaTest extends TestCase
{
    /**
     * @var \socialist\formula\Formula
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new Formula(
            '( p{Переменная $price} + 15%[Тут нужно вычислить процент наценки] ) * 2,65/*Тут пересчет по курсу*/'
        );
    }

    public function testClearFormula()
    {
        $this->assertEquals('(p+15%)*2,65', $this->parser->getSource());
    }

    public function testAllResults()
    {
        $parser = new Formula('2 * 2,65');
        $this->assertEquals('5.3', $parser->calculate());

        $parser = new Formula('2 * 2,65 + 25');
        $this->assertEquals('30.3', $parser->calculate());

        $parser = new Formula('2 * 2,65 + 25 / 3');
        $this->assertEquals('13.63', $parser->calculate());

        $parser = new Formula('2 + 3 * 2,65 + 25');
        $this->assertEquals('34.95', $parser->calculate());

        $parser = new Formula('2 + 3 * 2,65 + 25 - 26');
        $this->assertEquals('8.95', $parser->calculate());

        $parser = new Formula('2 + 3 - 4 * 2,65 + 25 - 26');
        $this->assertEquals('-6.6', $parser->calculate());

        $parser = new Formula('( 15 + p ) * 2,65');
        $parser->setVariable('p', 235);
        $this->assertEquals('662.5', $parser->calculate());

        $parser = new Formula('( 2 + ( 3 - 4 ) ) * 2,65 + 25 - 26');
        $this->assertEquals('1.65', $parser->calculate());

        $parser = new Formula('( 2 + ( 3 - 4 ) ) * ( 2.65 + ( 25 - 26 ) )');
        $this->assertEquals('1.65', $parser->calculate());

        echo "Large formula";
        $parser = new Formula('( p + ( 3 * 235 - 4 ) ) + 25');
        $parser->setVariable('p', 2);
        $this->assertEquals('728', $parser->calculate());
    }
}
