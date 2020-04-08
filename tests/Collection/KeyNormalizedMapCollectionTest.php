<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\KeyNormalizedMapCollection;
use Pes\Collection\Normalizer\KeyNormalizerInterface;
use Pes\Validator\IsObjectTypeValidator;

interface InterfaceForNormalizedKeyMapCollectionTest {}

class ObjectForNormalizedKeyMapCollectionTest {}

class InterfacedObjectForNormalizedKeyMapCollectionTest implements InterfaceForNormalizedKeyMapCollectionTest {}

class EntitkaForNormalizedKeyMapCollectionTest {
    public $a, $n, $x;
    public function __construct($a, $n, $x) {
        $this->a = $a;
        $this->n = $n;
        $this->x = $x;
    }
}

class LowerCaseKeyNormalizer implements KeyNormalizerInterface {
    public function normalizeKey($key) {
        return strtolower($key);
    }

    public function getOriginalKey($normalizedKey) {
        return strtoupper($normalizedKey);
    }
}

// není test pro nevé dodělou metodu has()!

/**
 * Description of NormalizedKeyMapCollectionTest
 * Nejde o čistý jednotkový test - testuje předevčím NormalizedKeyMapCollection, ale používá řadu dalších objektů.
 *
 * @author pes2704
 */
class KeyNormalizedMapCollectionTest extends TestCase {

    public function testConstructor() {
        $collection = new KeyNormalizedMapCollection(new LowerCaseKeyNormalizer());
        $this->assertInstanceOf('Pes\Collection\KeyNormalizedMapCollection', $collection);
    }

    public function testSetGetRemove() {
        $collection = new KeyNormalizedMapCollection(new LowerCaseKeyNormalizer());
        $collection->set('aAaa', 'aaacko');
        $collection->set('BBbb', 'beecko');
        $collection->set('Numero-Uno', 321321);
        $this->assertEquals(3, $collection->count());

        $obj = new ObjectForNormalizedKeyMapCollectionTest();
        $collection->set('Objekt', $obj);
        $this->assertEquals(4, $collection->count());

        $this->assertEquals('aaacko', $collection->get('aaaa'));
        $this->assertEquals('aaacko', $collection->get('aAaa'));
        $this->assertEquals('aaacko', $collection->get('Aaaa'));
        $this->assertEquals(321321, $collection->get('numero-uno'));
        $this->assertEquals($obj, $collection->get('OBJEKT'));
        $collection->remove('numero-UNO');
        $this->assertEmpty($collection->get('Numero-Uno'));
        $this->assertEquals(3, $collection->count());

    }

    public function testGetIterator() {
        // asocitivní pole
        $source = array(
            'primo'=>new ObjectForNormalizedKeyMapCollectionTest(),
            'secondo'=>new ObjectForNormalizedKeyMapCollectionTest(),
            'tertio'=>new EntitkaForNormalizedKeyMapCollectionTest('a', 2, new stdClass()));

        $collection = new KeyNormalizedMapCollection(new LowerCaseKeyNormalizer());
        foreach ($source as $key => $value) {
            $collection->set($key, $value);
        }
        $this->assertEquals('3', $collection->count());
        foreach ($collection as $key => $value) { // test get iterator
            $ret[$key] = $value;
        }
        $this->assertEquals($source, $ret);

    }

}
