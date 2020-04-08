<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\MapCollection;
use Pes\Validator\IsObjectTypeValidator;
use Pes\Comparator\OrderComparator;
use Pes\Comparator\SortComparator;
use Pes\Query\Order;
use Pes\Query\OrderingEnum;

interface InterfaceForMapCollectionTest {}

class ObjectForMapCollectionTest {}

class InterfacedObjectForMapCollectionTest implements InterfaceForMapCollectionTest {}

class EntitkaForMapCollectionTest {
    public $a, $n, $x;
    public function __construct($a, $n, $x) {
        $this->a = $a;
        $this->n = $n;
        $this->x = $x;
    }
}

// není test pro nevé dodělou metodu has()!

/**
 * Description of MapCollectionTest
 * Nejde o čistý jednotkový test - testuje předevčím MapCollection, ale používá řadu dalších objektů.
 *
 * @author pes2704
 */
class MapCollectionTest extends TestCase {

    public function testConstructor() {
        $collection = new MapCollection();
        $this->assertInstanceOf('Pes\Collection\MapCollection', $collection);
    }

    public function testSetGetRemove() {
        $collection = new MapCollection();
        $collection->set('aaaa', 'aaacko');
        $collection->set('numero', 321321);
        $this->assertEquals(2, $collection->count());

        $obj = new ObjectForMapCollectionTest();
        $collection->set('objekt', $obj);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals('aaacko', $collection->get('aaaa'));
        $this->assertEquals(321321, $collection->get('numero'));
        $this->assertEquals($obj, $collection->get('objekt'));

        $collection->set('objekt', $obj);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals($obj, $collection->get('objekt'));

        $collection->remove('numero');
        $this->assertEmpty($collection->get('numero'));
        $this->assertEquals(2, $collection->count());
        $collection->remove('numero');
        $this->assertEquals(2, $collection->count());
    }

    public function testGetIterator() {
        // asocitivní pole
        $source = array('aa'=>'acko', 'bb'=>'becko', 'cc'=>'cecko');
        $collection = new MapCollection();
        foreach ($source as $key => $value) {
            $collection->set($key, $value);
        }

        $this->assertEquals('3', $collection->count());
        foreach ($collection as $key => $value) { // test get iterator
            $ret[$key] = $value;
        }
        $this->assertEquals($source, $ret);
    }

    /**
     * Test řazení kolekce s použitím SortComparator
     */
    public function testOrder() {
        $source = array(
            new EntitkaForMapCollectionTest('004', '03', '1'),
            new EntitkaForMapCollectionTest('003', '01', '5'),
            new EntitkaForMapCollectionTest('001', '02', '2'),
            new EntitkaForMapCollectionTest('003', '02', '1'),
            new EntitkaForMapCollectionTest('003', '01', 'aa'),
            new EntitkaForMapCollectionTest('001', '02', '1'),
            new EntitkaForMapCollectionTest('005', '01', '1'),
            new EntitkaForMapCollectionTest('001', '01', '1'),
            new EntitkaForMapCollectionTest('004', '02', '1'),
            new EntitkaForMapCollectionTest('003', '01', '1'),
            new EntitkaForMapCollectionTest('004', '01', '1'),
        );
        $order = (new Order())->addOrdering('a', OrderingEnum::DESCENDING)->addOrdering('n', OrderingEnum::ASCENDING)->addOrdering('x', OrderingEnum::DESCENDING);
        $ordered = array(
            new EntitkaForMapCollectionTest('005', '01', '1'),
            new EntitkaForMapCollectionTest('004', '01', '1'),
            new EntitkaForMapCollectionTest('004', '02', '1'),
            new EntitkaForMapCollectionTest('004', '03', '1'),
            new EntitkaForMapCollectionTest('003', '01', 'aa'),
            new EntitkaForMapCollectionTest('003', '01', '5'),
            new EntitkaForMapCollectionTest('003', '01', '1'),
            new EntitkaForMapCollectionTest('003', '02', '1'),
            new EntitkaForMapCollectionTest('001', '01', '1'),
            new EntitkaForMapCollectionTest('001', '02', '2'),
            new EntitkaForMapCollectionTest('001', '02', '1'),
        );
        $collection = new MapCollection();
        foreach ($source as $key=>$value) {
            $collection->set($key, $value);
        }
        $collection->sort(OrderComparator::getCompareFunction($order));
        foreach ($collection as $key => $value) {
// iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
            $ret[] = $value;
        }
        $this->assertEquals($ordered, $ret);
    }

    /**
     * Test řazení kolekce s použitím SortComparator
     */
    public function testSort() {
        $source = array(
            new EntitkaForMapCollectionTest('004', '03', '1'),
            new EntitkaForMapCollectionTest('003', '01', '5'),
            new EntitkaForMapCollectionTest('001', '02', new EntitkaForMapCollectionTest('blx', 'ble', 'bli')),
            new EntitkaForMapCollectionTest('003', '02', '1'),
            new EntitkaForMapCollectionTest('003', '01', 'aa'),
            new EntitkaForMapCollectionTest('001', '02', new EntitkaForMapCollectionTest('bla', 'ble', 'bli')),
            new EntitkaForMapCollectionTest('005', '01', '1'),
            new EntitkaForMapCollectionTest('001', '01', '1'),
            new EntitkaForMapCollectionTest('004', '02', '1'),
            new EntitkaForMapCollectionTest('003', '01', '1'),
            new EntitkaForMapCollectionTest('004', '01', '1'),
        );
        $order = (new Order())->addOrdering('a', OrderingEnum::DESCENDING)->addOrdering('n', OrderingEnum::ASCENDING)->addOrdering('x', OrderingEnum::DESCENDING);
        $ordered = array(
            new EntitkaForMapCollectionTest('005', '01', '1'),
            new EntitkaForMapCollectionTest('004', '01', '1'),
            new EntitkaForMapCollectionTest('004', '02', '1'),
            new EntitkaForMapCollectionTest('004', '03', '1'),
            new EntitkaForMapCollectionTest('003', '01', 'aa'),
            new EntitkaForMapCollectionTest('003', '01', '5'),
            new EntitkaForMapCollectionTest('003', '01', '1'),
            new EntitkaForMapCollectionTest('003', '02', '1'),
            new EntitkaForMapCollectionTest('001', '01', '1'),
            new EntitkaForMapCollectionTest('001', '02', new EntitkaForMapCollectionTest('blx', 'ble', 'bli')),
            new EntitkaForMapCollectionTest('001', '02', new EntitkaForMapCollectionTest('bla', 'ble', 'bli')),
        );
        $collection = new MapCollection();
        foreach ($source as $key=>$value) {
            $collection->set($key, $value);
        }
        $collection->sort(SortComparator::getCompareFunction($order));
        foreach ($collection as $key => $value) {
// iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
            $ret[] = $value;
        }
        $this->assertEquals($ordered, $ret);
    }
}
