<?php
namespace TimoLehnertz\formula;


/**
 * 
 * @author Timo Lehnertz
 *
 */
class ExpressionNotFoundException extends \Exception {
  
  public function __construct(string $message, $source = "", int $index = -1) {
    if(is_array($source)) { // array of tokens
      $sourceTmp = "";
      foreach ($source as $token) {
        $sourceTmp .= $token->value;
      }
      $source = $sourceTmp;
    }
    $indexStr = "";
    if($index >= 0) {
      $indexStr = " At position: $index";
    }
    parent::__construct("$message. Formula: \"$source\" $indexStr");
  }
}