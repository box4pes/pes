<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Matcher\CompareMatcher;
/**
 * Description of CompareMatcherTest
 *
 * @author pes2704
 */
class CompareMatcherTest extends TestCase {
    /**
     * testuje "=", "!=":, "<>":, "<", "<=":, ">", ">="
     */
    public function testMatch() {
        $this->assertTrue((new CompareMatcher('='))->match(555, 555));
        $this->assertFalse((new CompareMatcher('='))->match(55, 555));

        $this->assertTrue((new CompareMatcher('!='))->match(0, 555));
        $this->assertFalse((new CompareMatcher('!='))->match(555, 555));

        $this->assertTrue((new CompareMatcher('<>'))->match('AA', 555));
        $this->assertFalse((new CompareMatcher('<>'))->match('555', 555));

        $this->assertTrue((new CompareMatcher('<'))->match('554', 555));
        $this->assertFalse((new CompareMatcher('<'))->match('5545', 555));

        $this->assertTrue((new CompareMatcher('<='))->match(554, 555));
        $this->assertTrue((new CompareMatcher('<='))->match(555, 555));
        $this->assertFalse((new CompareMatcher('<='))->match(556, 555));

        $this->assertTrue((new CompareMatcher('>'))->match(123456, 555));
        $this->assertFalse((new CompareMatcher('>'))->match(12, 555));

        $this->assertTrue((new CompareMatcher('>='))->match(789, 555));
        $this->assertTrue((new CompareMatcher('>='))->match(555, 555));
        $this->assertFalse((new CompareMatcher('>='))->match(7, 555));
    }
}
