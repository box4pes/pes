<?php
use PHPUnit\Framework\TestCase;

use Pes\Query\Matcher\LikeMatcher;

/**
 * Description of InMatcherTest
 *
 * @author pes2704
 */
class LikeMatcherTest extends TestCase {
    /**
     * testuje x IN array
     */
    public function testMatchString() {
        $this->assertTrue((new LikeMatcher())->match('A', 'A'));
        $this->assertTrue((new LikeMatcher())->match('A', 'a'));
        $this->assertTrue((new LikeMatcher())->match('a', 'A'));
        $this->assertTrue((new LikeMatcher())->match('AbcD~ˇ^˘°˛`˙OoPp', 'aBCd~ˇ^˘°˛`˙oOpP'));
    }
    
    public function testMatchEscapedString() {    
        $this->assertTrue((new LikeMatcher())->match('A', '%A'));
        $this->assertTrue((new LikeMatcher())->match('A', 'A%'));
        $this->assertTrue((new LikeMatcher())->match('A', '%A%'));
        $this->assertTrue((new LikeMatcher())->match('Aqua', '_q%'));
        $this->assertTrue((new LikeMatcher())->match('Aqua', '_q__'));
        $this->assertFalse((new LikeMatcher())->match('Aqua', '_q'));
        $this->assertTrue((new LikeMatcher())->match('A', '%'));
        $this->assertTrue((new LikeMatcher())->match('A', '_'));
        $this->assertTrue((new LikeMatcher())->match('A', '_%'));
        $this->assertFalse((new LikeMatcher())->match('A', '__'));
    }

    public function testMatchStringWithWildcards() {
        $this->assertTrue((new LikeMatcher())->match('A_%a', '_\_\%%'));
        $this->assertTrue((new LikeMatcher())->match('A_%a', '_\_\%_'));
    }
    
    public function testMatchEscapedStringWithChangedEscapeCharacter() {    
        $this->assertTrue((new LikeMatcher('W'))->match('A_%a', '_W_W%%'));
        $this->assertTrue((new LikeMatcher('W'))->match('A_%a', '_W_W%_'));
    }
}