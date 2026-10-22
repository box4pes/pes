<?php
declare(strict_types=1);

namespace Pes\Model\RowData;

use PHPUnit\Framework\TestCase;

/**
 * Description of RowDataTest
 *
 * @author pes2704
 */
class RowDataTest extends TestCase {

    public static function mock() {
    }

    public static function setUpBeforeClass(): void {
    }

    protected function setUp(): void {

    }

    public function testConstructWithNoData() {
        $rowData = new RowData();
        $this->assertInstanceOf(RowData::class, $rowData);
        $this->assertInstanceOf(RowDataInterface::class, $rowData);
    }

    public function testConstructWithArray() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertInstanceOf(RowData::class, $rowData);
        $this->assertInstanceOf(RowDataInterface::class, $rowData);
    }

    public function testClone() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $clone = clone $rowData;
        $this->assertInstanceOf(RowDataInterface::class, $clone);
    }

    public function testOffsetSetGet() {
        $originArray = ['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()];
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertTrue($rowData->offsetExists(1));
        $this->assertIsString($rowData->offsetGet(1));
        $this->assertTrue($rowData->offsetExists('abcd'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('abcd'));

        $rowData->offsetUnset('abcd');  // jen changed
        $this->assertTrue($rowData->offsetExists('abcd'));
        $this->assertTrue($rowData->offsetExists('null'));
        $this->assertNull($rowData->offsetGet('null'));
        $rowData->offsetSet('efgh', new \stdClass());
        $this->assertFalse($rowData->offsetExists('efgh'));

        $oldDataArray = $rowData->getArrayCopy();
        $this->assertIsArray($oldDataArray);
        $this->assertCount(4, $oldDataArray);
        $this->assertEquals(['a', 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()], $oldDataArray);

        $changedDataArrayObject = $rowData->yieldChangedAsArrayObject();
        $this->assertInstanceOf(\ArrayObject::class, $changedDataArrayObject);
        $this->assertCount(2, $changedDataArrayObject);

        $changedData = $rowData->fetchChanged();
        $this->assertIsArray($changedData);
        $this->assertCount(2, $changedData);
        $this->assertEquals(['abcd'=>null, 'efgh'=>new \stdClass()], $changedData);

        $this->assertFalse($rowData->offsetExists('abcd'));
        $this->assertTrue($rowData->offsetExists('efgh'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('efgh'));
        $this->assertTrue($rowData->offsetExists('null'));
        $this->assertNull($rowData->offsetGet('null'));

        $oldDataArray2 = $rowData->getArrayCopy();
        $this->assertEquals(['a', null, 'null'=>null, 1=>'TRTRTS', 'efgh'=>new \stdClass()], $oldDataArray2);

        $changedDataArrayObject2 = $rowData->yieldChangedAsArrayObject();
        $this->assertInstanceOf(\ArrayObject::class, $changedDataArrayObject2);
        $this->assertCount(0, $changedDataArrayObject2);

        $changedData2 = $rowData->fetchChanged();
        $this->assertIsArray($changedData2);
        $this->assertCount(0, $changedData2);
    }

    public function testNoChangeAfterSetSameValues() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->fetchChanged();
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', null);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('abcd', $rowData->offsetGet('abcd'));
        $this->assertFalse($rowData->isChanged());
        $changed = $rowData->fetchChanged();
        $this->assertCount(0, $changed);
    }

    public function testAdd() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());

        $rowData->offsetSet(2, 'TRTRTS');
        $rowData->offsetSet('newnull', null);
        $this->assertTrue($rowData->isChanged());
        $ch = $rowData->fetchChanged();
        $this->assertEquals([2=>'TRTRTS', 'newnull'=>null], $ch);
    }

    public function testScalarValueChange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS1');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([1=>'TRTRTS1'], $rowData->fetchChanged());
        //opakovaná změna dat
        $rowData->offsetSet(1, 'TRTRTS2');
        $this->assertTrue($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS3');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([1=>'TRTRTS3'], $rowData->fetchChanged());
    }

    public function testObjectExchange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $object = new \stdClass();
        $rowData->offsetSet('abcd', $object);
        $this->assertTrue($rowData->isChanged());
        $changed = $rowData->fetchChanged();
        $this->assertCount(1, $changed);
        $this->assertEquals(['abcd'=>new \stdClass()], $changed);
        $this->assertTrue($rowData->offsetExists('abcd'));
        $this->assertInstanceOf(\stdClass::class, $rowData->offsetGet('abcd'));
    }

    public function testObjectAttributeChange() {
        $object = new \stdClass();
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>$object]);
        $this->assertFalse($rowData->isChanged());
        $object->new = 'UZUZUUZ';
        $rowData->offsetSet('abcd', $object);
        $this->assertFalse($rowData->isChanged());
        $this->assertCount(0, $rowData->fetchChanged());
    }

    public function testNullToNotnullChange() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', 'not null');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals(['null'=>'not null'], $rowData->fetchChanged());
    }

    public function testNotnullToNullChange() {
        $rowData = new RowData(['a', null, 'null'=>'not null', 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet('null', null);
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals(['null'=>null], $rowData->fetchChanged());
    }

    public function testFetchChanged() {
        $rowData = new RowData(['a', null, 'null'=>null, 1=>'TRTRTS', 'abcd'=>new \stdClass()]);
        $this->assertFalse($rowData->isChanged());
        $rowData->offsetSet(1, 'TRTRTS');
        $this->assertFalse($rowData->isChanged());
        $this->assertCount(0, $rowData->fetchChanged());
        $rowData->offsetSet(2, 'TRTRTS');
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([2=>'TRTRTS'], $rowData->yieldChangedAsArrayObject()->getArrayCopy());
        $object = new \stdClass();
        $rowData->offsetSet('abcd', $object);
        $this->assertTrue($rowData->isChanged());
        $this->assertEquals([2=>'TRTRTS', 'abcd'=>$object], $rowData->yieldChangedAsArrayObject()->getArrayCopy());
        $this->assertCount(2, $rowData->fetchChanged());

    }
}
