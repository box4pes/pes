<?php
namespace Pes\Query\Matcher;

/**
 *
 * @author pes2704
 */
interface MatcherInterface {
    public function match($value, $pattern);
}
