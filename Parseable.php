<?php
namespace socialistFork\formula;

/**
 *
 * @author Timo Lehnertz
 * 
 */
interface Parseable {

  /**
   * iterates over the token aray starting at $currentIndex.
   * If it cant parse itself out of the tokens this function will return false
   * and reset the $currentIndex to what it was before so the next contestant has a chance to be parsed
   *
   * @param array $tokens
   * @param int $currentIndex
   * @return bool true if parsing was succsessfull
   */
  public function parse(array &$tokens, int &$index): bool;
}
