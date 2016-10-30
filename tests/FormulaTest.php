<?php

/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 28.10.16
 * Time: 20:13
 */
class FormulaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \socialist\formula\Formula
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new \socialist\formula\Formula('( p{Переменная $price} + 15%[Тут нужно вычислить процент наценки] ) * 2,65/*Тут пересчет по курсу*/');
    }

    public function testClearFormula()
    {
        $this->assertEquals( '(p+15%)*2,65', $this->parser->getSource() );
    }

    public function testAllResults()
    {
        $parser = new \socialist\formula\Formula('2 * 2,65');
        $parser->parse();
        $this->assertEquals( '5.3', $parser->calculate() );

        $parser = new \socialist\formula\Formula('2 * 2,65 + 25');
        $parser->parse();
        $this->assertEquals( '30.3', $parser->calculate() );

        $parser = new \socialist\formula\Formula('2 * 2,65 + 25 / 3');
        $parser->parse();
        $this->assertEquals( '13.63', $parser->calculate() );

        $parser = new \socialist\formula\Formula('2 + 3 * 2,65 + 25');
        $parser->parse();
        $this->assertEquals( '34.95', $parser->calculate() );

        $parser = new \socialist\formula\Formula('2 + 3 * 2,65 + 25 - 26');
        $parser->parse();
        $this->assertEquals( '8.95', $parser->calculate() );

        $parser = new \socialist\formula\Formula('2 + 3 - 4 * 2,65 + 25 - 26');
        $parser->parse();
        $this->assertEquals( '-6.6', $parser->calculate() );

        $parser = new \socialist\formula\Formula('( 15 + p ) * 2,65');
        $parser->setVariable( 'p', 235 );
        $parser->parse();
        $this->assertEquals( '662.5', $parser->calculate() );

        $parser = new \socialist\formula\Formula('( 2 + ( 3 - 4 ) ) * 2,65 + 25 - 26');
        $parser->parse();
        $this->assertEquals( '1.65', $parser->calculate() );

        $parser = new \socialist\formula\Formula('( 2 + ( 3 - 4 ) ) * ( 2,65 + ( 25 - 26 ) )');
        $parser->parse();
        $this->assertEquals( '1.65', $parser->calculate() );

        echo "Large formula";
        $parser = new \socialist\formula\Formula('( p + ( 3 * 235 - 4 ) ) + 25');
        $parser->setVariable( 'p', 2 );
        $parser->parse();
        $this->assertEquals( '728', $parser->calculate() );
    }
}
