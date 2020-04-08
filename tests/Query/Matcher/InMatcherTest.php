<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Matcher\InMatcher;

/**
 * Description of InMatcherTest
 *
 * @author pes2704
 */
class InMatcherTest extends TestCase {
    /**
     * testuje x IN array
     */
    public function testMatch() {
        $this->assertTrue((new InMatcher())->match('A', array('p', 'Q', 'A')));
        $this->assertFalse((new InMatcher())->match('A', array('p', 'Q', 'B')));        
        $this->assertFalse((new InMatcher())->match('A', array('Aa')));
        $this->assertTrue((new InMatcher())->match(222, array('222')));
    }
    
    public function testMatchWithScalarAndNullPattern() {    
        $this->assertTrue((new InMatcher())->match(222, '222'));
        $this->assertFalse((new InMatcher())->match(222, NULL));
        $this->assertFalse((new InMatcher())->match(NULL, 222));

    }
}