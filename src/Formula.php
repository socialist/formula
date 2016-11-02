<?php
/**
 * Created by PhpStorm.
 * User: seregas
 * Date: 28.10.16
 * Time: 20:01
 */

namespace socialist\formula;


use socialist\formula\expression\Division;
use socialist\formula\expression\Increment;
use socialist\formula\expression\Multiplication;
use socialist\formula\expression\Operator;
use socialist\formula\expression\Subtraction;
use socialist\formula\operator\Double;

class Formula
{
    public $expressions  = [];

    protected $variables = [];

    protected $source    = '';
    /**
     * @var Operator
     */
    protected $result;

    /**
     * Parser constructor.
     * @param $value
     * @throws \Exception
     */
    public function __construct( $value )
    {
        if ( !is_string( $value ) ) {
            throw new \Exception( "Formula must be a string" );
        }

        $this->source = $this->clear( $value );
    }

    /**
     * @param $key
     * @param $value
     */
    public function setVariable( $key, $value )
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return mixed|string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Operator
     */
    public function getExpression()
    {
        $key = substr( $this->source, 1, -1 );
        if ( array_key_exists( $key, $this->expressions ))
            return $this->expressions[ $key ];
    }

    /**
     * Formula parse
     */
    public function parse()
    {
        while ( preg_match( '/\(((?:(?>[^()]+)|(?R))*)\)/i', $this->source, $results ) ) {
            $key = $this->generateKey();
            $formula = new static( $results[1] );
            foreach ( $this->variables as $key => $var ) {
                $formula->setVariable( $key, $var );
            }
            $formula->parse();

            $this->expressions[ $key ] = $formula->getExpression();
            $this->source = str_replace( $results[0], '{' . $key . '}', $this->source );
        }

        while ( preg_match( '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)\*([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i', $this->source, $results ) ) {
            $left = $this->getExpressionObject( $results[2] );
            $right = $this->getExpressionObject( $results[3] );
            $key = $this->generateKey();

            $this->expressions[ $key ] = new Multiplication( $left, $right );

            $this->source = str_replace( $results[1], '{' . $key . '}', $this->source );
        }

        while ( preg_match( '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)\/([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i', $this->source, $results ) ) {
            $left = $this->getExpressionObject( $results[2] );
            $right = $this->getExpressionObject( $results[3] );
            $key = $this->generateKey();

            $this->expressions[ $key ] = new Division( $left, $right );
            $this->source = str_replace( $results[1], '{' . $key . '}', $this->source );
        }


        while ( preg_match( '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)([\+|-])([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i', $this->source, $results ) ) {
            $left = $this->getExpressionObject( $results[2] );
            $right = $this->getExpressionObject( $results[4] );
            $key = $this->generateKey();

            $className = ( $results[3] == '+' )
                ? '\socialist\formula\expression\Increment'
                : '\socialist\formula\expression\Subtraction';

            $this->expressions[ $key ] = new $className( $left, $right );
            $this->source = str_replace( $results[1], '{' . $key . '}', $this->source );
        }

        $this->result = $this->getExpressionObject( $this->source );
    }

    public function calculate()
    {
        $key = substr( $this->source, 1, -1 );
        if ( array_key_exists( $key, $this->expressions ))
            return $this->expressions[ $key ]->calculate( $this->expressions[ $key ] );
    }

    /**
     * @param $source
     * @return mixed
     */
    private function clear( $source )
    {
        $patterns = [
            '/\/\*(.*)\*\//i',
            '/\{(.*)\}/i',
            '/\[(.*)\]/i',
            '/[\s]+/i',
        ];
        return preg_replace( $patterns, '', $source );
    }

    private function generateKey()
    {
        return $uid = md5(uniqid(rand(), true));
    }

    private function getExpressionObject( $expression )
    {
        if ( preg_match( '/\{([\w\d]+)\}/', $expression, $result ) ) {
            return $this->expressions[$result[1]];
        } else if ( strpos( $expression, '.' ) !== false || strpos( $expression, ',' ) !== false ) {
            return new \socialist\formula\operator\Double( $expression );
        } else if ( strpos( $expression, '%' ) !== false ) {
            return new \socialist\formula\operator\Percent( $expression );
        } else if ( ( int ) $expression > 0 ) {
            return new \socialist\formula\operator\Integer( $expression );
        } else {
            $variable = new \socialist\formula\operator\Variable( $expression );

            if ( array_key_exists( $expression, $this->variables ) ) {
                $variable->setValue( $this->variables[ $expression ] );
            }

            return $variable;
        }
    }
}