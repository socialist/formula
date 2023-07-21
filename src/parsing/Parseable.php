<?php
namespace TimoLehnertz\formula\parsing;

/**
 *
 * @author Timo Lehnertz
 * 
 */
interface Parseable {

  /**
   * Should iterate over the token aray starting at $currentIndex not looking back
   * If parsing is not succsessfull this function must reset $currentIndex to what it was initially
   * Must not alter tokens array
   *
   * @param array $tokens
   * @param int $currentIndex
   * @return mixed false if parsing was not succsessfull, the parsed result if succsessfull
   */
  public static function parse(array &$tokens, int &$index);
}
