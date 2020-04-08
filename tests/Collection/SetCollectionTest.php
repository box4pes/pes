<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\SetCollection;
use Pes\Comparator\OrderComparator;
use Pes\Comparator\SortComparator;
use Pes\Query\Order;
use Pes\Query\OrderingEnum;

class EntitkaForSetCollectionTest {
    public $a, $n, $x;
    public function __construct($a, $n, $x) {
        $this->a = $a;
        $this->n = $n;
        $this->x = $x;
    }
}

// není test pro nevé dodělou metodu has()!

/**
 * Description of SetCollectionTest
 * Nejde o čistý jednotkový test - testuje předevčím SetCollection, ale používá řadu dalších objektů.
 *
 * @author pes2704
 */
class SetCollectionTest extends TestCase {
    public function testConstructor() {
        $collection = new SetCollection();
        $this->assertInstanceOf('Pes\Collection\SetCollection', $collection);
        $collection = new SetCollection([1, 2, 3]);
        $this->assertInstanceOf('Pes\Collection\SetCollection', $collection);
        $this->assertEquals(3, $collection->count());
    }

    public function testSetRemoveCount() {
        $collection = new SetCollection();
        $obj = new EntitkaForSetCollectionTest('a', 'n', 'x');
        $collection->set($obj);
        $collection->set(new stdClass());
        $this->assertEquals(2, $collection->count());
        $collection->remove($obj);
        $this->assertEquals(1, $collection->count());
        $collection->remove($obj);
        $this->assertEquals(1, $collection->count());

        $collection = new SetCollection([1, 2, 3]);
        $this->assertEquals(3, $collection->count());
        $obj = new EntitkaForSetCollectionTest('a', 'n', 'x');
        $collection->set($obj);
        $this->assertEquals(4, $collection->count());
        $collection->set($obj);
        $this->assertEquals(4, $collection->count());
        $collection->remove($obj);
        $this->assertEquals(3, $collection->count());

    }

    public function testOrder() {
        $source = array(
            new EntitkaForSetCollectionTest('004', '03', '1'),
            new EntitkaForSetCollectionTest('003', '01', '5'),
            new EntitkaForSetCollectionTest('001', '02', '2'),
            new EntitkaForSetCollectionTest('003', '02', '1'),
            new EntitkaForSetCollectionTest('003', '01', 'aa'),
            new EntitkaForSetCollectionTest('001', '02', '1'),
            new EntitkaForSetCollectionTest('005', '01', '1'),
            new EntitkaForSetCollectionTest('001', '01', '1'),
            new EntitkaForSetCollectionTest('004', '02', '1'),
            new EntitkaForSetCollectionTest('003', '01', '1'),
            new EntitkaForSetCollectionTest('004', '01', '1'),
        );
        $order = (new Order())->addOrdering('a', OrderingEnum::DESCENDING)->addOrdering('n', OrderingEnum::ASCENDING)->addOrdering('x', OrderingEnum::DESCENDING);
        $ordered = array(
            new EntitkaForSetCollectionTest('005', '01', '1'),
            new EntitkaForSetCollectionTest('004', '01', '1'),
            new EntitkaForSetCollectionTest('004', '02', '1'),
            new EntitkaForSetCollectionTest('004', '03', '1'),
            new EntitkaForSetCollectionTest('003', '01', 'aa'),
            new EntitkaForSetCollectionTest('003', '01', '5'),
            new EntitkaForSetCollectionTest('003', '01', '1'),
            new EntitkaForSetCollectionTest('003', '02', '1'),
            new EntitkaForSetCollectionTest('001', '01', '1'),
            new EntitkaForSetCollectionTest('001', '02', '2'),
            new EntitkaForSetCollectionTest('001', '02', '1'),
        );
        $collection = new SetCollection($source);
        $collection->sort(OrderComparator::getCompareFunction($order));
        foreach ($collection as $key => $value) {
// iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
            $ret[] = $value;
        }
        $this->assertEquals($ordered, $ret);
    }

    public function testSort() {
        $source = array(
            new EntitkaForSetCollectionTest('004', '03', '1'),
            new EntitkaForSetCollectionTest('003', '01', '5'),
            new EntitkaForSetCollectionTest('001', '02', new EntitkaForSetCollectionTest('blx', 'ble', 'bli')),
            new EntitkaForSetCollectionTest('003', '02', '1'),
            new EntitkaForSetCollectionTest('003', '01', 'aa'),
            new EntitkaForSetCollectionTest('001', '02', new EntitkaForSetCollectionTest('bla', 'ble', 'bli')),
            new EntitkaForSetCollectionTest('005', '01', '1'),
            new EntitkaForSetCollectionTest('001', '01', '1'),
            new EntitkaForSetCollectionTest('004', '02', '1'),
            new EntitkaForSetCollectionTest('003', '01', '1'),
            new EntitkaForSetCollectionTest('004', '01', '1'),
        );
        $order = (new Order())->addOrdering('a', OrderingEnum::DESCENDING)->addOrdering('n', OrderingEnum::ASCENDING)->addOrdering('x', OrderingEnum::DESCENDING);
        $ordered = array(
            new EntitkaForSetCollectionTest('005', '01', '1'),
            new EntitkaForSetCollectionTest('004', '01', '1'),
            new EntitkaForSetCollectionTest('004', '02', '1'),
            new EntitkaForSetCollectionTest('004', '03', '1'),
            new EntitkaForSetCollectionTest('003', '01', 'aa'),
            new EntitkaForSetCollectionTest('003', '01', '5'),
            new EntitkaForSetCollectionTest('003', '01', '1'),
            new EntitkaForSetCollectionTest('003', '02', '1'),
            new EntitkaForSetCollectionTest('001', '01', '1'),
            new EntitkaForSetCollectionTest('001', '02', new EntitkaForSetCollectionTest('blx', 'ble', 'bli')),
            new EntitkaForSetCollectionTest('001', '02', new EntitkaForSetCollectionTest('bla', 'ble', 'bli')),
        );
        $collection = new SetCollection($source);
        $collection->sort(SortComparator::getCompareFunction($order));
        foreach ($collection as $key => $value) {
// iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
            $ret[] = $value;
        }
        $this->assertEquals($ordered, $ret);
    }
}
