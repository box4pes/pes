<?php
declare(strict_types=1);

namespace Pes\Model\RowData\Filter;

use PHPUnit\Framework\TestCase;

/**
 * Description of NominateFilterTest
 *
 * @author pes2704
 */
class NominateFilterTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {

    }

    public function testConstruct() {
        $filter = new NominateFilter(new \ArrayIterator([1,2,3]));
        $this->assertInstanceOf(NominateFilter::class, $filter);
        $this->assertInstanceOf(NominateFilterInterface::class, $filter);
    }

    public function testEmpty() {
        $filter = new NominateFilter(new \ArrayIterator([1,2,3]));
        $this->assertCount(0, $filter);
        $result = iterator_to_array($filter);
        $this->assertCount(0, $result);
    }


    public function testFilter() {
        $filter = new NominateFilter(new \ArrayIterator(['a'=>1,'b'=>2,'c'=>3, 'd'=>4, 'e'=>5]));
        $this->assertCount(0, $filter);
        $filter->nominate(['b', 'e']);
        $this->assertCount(2, $filter);
        $result = iterator_to_array($filter);
        $this->assertCount(2, $result);
        $this->assertEquals(['b'=>2, 'e'=>5], $result);
    }


}
