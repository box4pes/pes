<?php
declare(strict_types=1);

namespace Pes\Model\RowData;

use PHPUnit\Framework\TestCase;
use Pes\Model\RowData\Filter\NominateFilter;

/**
 * Description of FilteredRowDataTest
 *
 * @author pes2704
 */
class FilteredRowDataTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {
    }

    public function testAdd() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);

        $rowData->offsetSet(2, 'TRTRTS');
        $rowData->offsetSet('newnull', null);
        $this->assertTrue($rowData->isChanged());

        $changed = $rowData->yieldChangedAsArrayObject();
        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $changed->getArrayCopy());

        $filter = new NominateFilter($changed->getIterator());
        $filter->nominate(array_keys($changed->getArrayCopy()));
        $this->assertCount(2, $filter);
        $actual = iterator_to_array($filter);
        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $actual);
    }

}
