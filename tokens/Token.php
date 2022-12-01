<?php
namespace socialistFork\formula\tokens;

/**
 *
 * @author timo
 *        
 */
class Token {

  /**
   * Name of this token
   * @readonly
   * @var string
   */
  public string $name;

  /**
   * Content of this token. Unchanged string
   * @readonly
   * @var string
   */
  public string $value = "";

  /**
   * Global position of the this token in the source string. used for throwing more accurate Exceptions
   * @readonly
   * @var int
   */
  public int $position;

  public function __construct(string $name, string $value, int $position) {
    $this->name = $name;
    $this->value = $value;
    $this->position = $position;
  }
}