<?php
namespace socialist\formula;


use socialist\formula\expression\Division;
use socialist\formula\expression\Increment;
use socialist\formula\expression\Multiplication;
use socialist\formula\expression\Operator;
use socialist\formula\expression\Subtraction;
use socialist\formula\operator\Expression;
use socialist\formula\operator\Variable;

class Formula
{
    /**
     * @var Operator[]
     */
    public $expressions  = [];

    /**
     * @var Variable[]
     */
    protected $variables = [];

    /**
     * @var string
     */
    protected $source = '';

    /**
     * @var Operator
     */
    protected $result;

    /**
     * Formula constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->source = $this->clear($value);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setVariable(string $key, string $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return null|Operator
     * @throws ExpressionNotFoundException
     */
    public function getExpression(): ?Operator
    {
        $key = substr($this->source, 1, -1);

        if (array_key_exists($key, $this->expressions)) {
            return $this->expressions[$key];
        }

        throw new ExpressionNotFoundException('Expression not found: key - ' . $key);
    }

    /**
     * @return float
     */
    public function calculate(): float
    {
        $this->parse();
        $expression = $this->getExpression();
        return $expression->calculate($expression);
    }

    /**
     * Clear all comments in source
     *
     * @param $source
     * @return string
     */
    private function clear(string $source): string
    {
        $patterns = [
            '/\/\*(.*)\*\//i',
            '/\{(.*)\}/i',
            '/\[(.*)\]/i',
            '/[\s]+/i',
        ];
        return preg_replace($patterns, '', $source);
    }

    /**
     * Generate random key
     *
     * @return string
     */
    private function generateKey(): string
    {
        return $uid = md5(uniqid(rand(), true));
    }

    /**
     * @param string $expression
     * @return Expression
     */
    private function getExpressionObject(string $expression): Expression
    {
        if (preg_match('/\{([\w\d]+)\}/', $expression, $result)) {
            return $this->expressions[$result[1]];
        } else {
            return ExpressionFactory::factory($expression, $this->variables);
        }
    }

    /**
     * Formula parse
     */
    public function parse()
    {
        $patterns = [
            '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)(\*)([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i',
            '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)(\/)([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i',
            '/(([\d\.,%]+|[^\{\}]|[\{\w\d\}]+)([\+|-])([\d\.,%]+|[^\{\}]|[\{\w\d\}]+))/i',
        ];

        $operators = [
            '*' => Multiplication::class,
            '/' => Division::class,
            '-' => Subtraction::class,
            '+' => Increment::class,
        ];

        // Выражение в скобках
        while (preg_match('/\(((?:(?>[^()]+)|(?R))*)\)/i', $this->source, $results)) {
            $key = $this->generateKey();
            $formula = new static($results[1]);
            foreach ($this->variables as $key => $var) {
                $formula->setVariable($key, $var);
            }
            $formula->parse();

            $this->expressions[$key] = $formula->getExpression();
            $this->source = str_replace($results[0], '{' . $key . '}', $this->source);
        }

        foreach ($patterns as $pattern) {
            while (preg_match($pattern, $this->source, $results)) {
                try {
                    $left = $this->getExpressionObject( $results[2] );
                    $right = $this->getExpressionObject( $results[4] );
                    $key = $this->generateKey();

                    $this->expressions[ $key ] = new $operators[$results[3]]( $left, $right );

                    $this->source = str_replace( $results[1], '{' . $key . '}', $this->source );
                } catch(\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        $this->result = $this->getExpressionObject( $this->source );
    }
}