<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Matcher\NotInMatcher;

/**
 * Description of NotInMatcherTest
 *
 * @author pes2704
 */
class NotInMatcherTest extends TestCase {
    /**
     * testuje x IN array
     */
    public function testMatch() {
        $this->assertFalse((new NotInMatcher())->match('A', array('p', 'Q', 'A')));
        $this->assertTrue((new NotInMatcher())->match('A', array('p', 'Q', 'B')));
        $this->assertTrue((new NotInMatcher())->match('A', array('Aa')));
        $this->assertFalse((new NotInMatcher())->match(222, array('222')));
    }
    
    public function testMatchWithScalarAndNullPattern() {    
        $this->assertFalse((new NotInMatcher())->match(222, '222'));
        $this->assertTrue((new NotInMatcher())->match(222, NULL));
        $this->assertTrue((new NotInMatcher())->match(NULL, 222));

    }
}