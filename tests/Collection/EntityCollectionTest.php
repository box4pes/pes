<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\EntityCollection;
use Pes\Validator\IsObjectTypeValidator;
// používá OrderComparatorClassMethods, SortComparatorClassMethods protože použitá entita má gettery a settery a private vlastnosti
use Pes\Comparator\OrderComparatorClassMethods;
use Pes\Comparator\SortComparatorClassMethods;
use Pes\Query\Order;
use Pes\Query\OrderingEnum;

use Pes\Entity\Persistable\PersistableEntityInterface;
use Pes\Entity\Persistable\PersistableEntityAbstract;
use Pes\Entity\Persistable\IdentityInterface;
use Pes\Entity\Persistable\Identity;

class ObjectForEntityCollectionTest extends PersistableEntityAbstract {}

abstract class InterfacedObjectForEntityCollectionTest implements PersistableEntityInterface{}

class EntitkaForEntityCollectionTest extends PersistableEntityAbstract {
    private $a, $n, $x;
    public function getA() {
        return $this->a;
    }

    public function getN() {
        return $this->n;
    }

    public function getX() {
        return $this->x;
    }

    public function setA($a) {
        $this->a = $a;
        return $this;
    }

    public function setN($n) {
        $this->n = $n;
        return $this;
    }

    public function setX($x) {
        $this->x = $x;
        return $this;
    }
}

/**
 * Description of EntityCollectionTest
 * Nejde o čistý jednotkový test - testuje předevčím EntityCollection, ale používá řadu dalších objektů.
 * @author pes2704
 */
class EntityCollectionTest extends TestCase {
    public function testIncomplete()
    {    // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }    
    //
    // a) změnil jsem rohraní remove na removeByIdentity -> jiné chování proti tomu když byl psá původní test
    // b) Entita asi bude jiné (po vývoji Aggregate) a zřejmě i EntityCollection
    //

    
    
    
//    public function testConstructor() {
//        try {
//            $collection = new EntityCollection();
//        } catch (TypeError $uve) {
//            $this->assertStringStartsWith('Argument 2 passed to', $uve->getMessage());
//        }
//        //TypeError: Argument 2 passed to Pes\Collection\EntityCollection::__construct() must be an instance of Pes\Validator\ValidatorInterface, none given, called in C:\xampp\htdocs\Pes\Pes\tests\Collection\EntityCollectionTest.php on line 56
//
//        $validator = new IsTypeValidator('InterfacedObjectForEntityCollectionTest');
////        $validatorMock = $this->getMockBuilder('IsTypeValidator')->getMock();
////        $validatorMock->eexpects($this->any())-
//        $collection = new EntityCollection(NULL, $validator);
//        $this->assertInstanceOf('Pes\Collection\EntityCollection', $collection);
//    }
//    
//    public function testSetAndRemoveEntityWithoutIdentity() {
//        $collection = new EntityCollection(NULL, new IsTypeValidator('Pes\Entity\Persistable\PersistableEntityInterface'));
//        $entitka = new EntitkaForEntityCollectionTest();  // bezidentity
//        $collection->set($entitka);
//        $collection->set($entitka);  //podruhé identický objekt
//        $this->assertEquals(1, $collection->count());
//        $entitka2 = new EntitkaForEntityCollectionTest();  // bezidentity
//        $collection->set($entitka2);
//        $this->assertEquals(2, $collection->count());
//        $collection->removeByIdentity($entitka);
//        $this->assertEquals(1, $collection->count());
//        $collection->remove($entitka);   // není co remove, už tama není
//        $this->assertEquals(1, $collection->count());
//        $collection->set($entitka);
//        $this->assertEquals(2, $collection->count());
//    }
//    
//    public function testSetWithIdentityAndGetbyidentityAndRemoveAndRemoveByIdentityForEntityWithIdentity() {
//        $collection = new EntityCollection(NULL, new IsTypeValidator('Pes\Entity\Persistable\PersistableEntityInterface'));
//        $entitka8 = new EntitkaForEntityCollectionTest();
//        $entitka8->setIdentity(new Identity(8));
//        $collection->set($entitka8);
//        $collection->set($entitka8);  //podruhé identický objekt - nepřidá nic
//        $this->assertEquals(1, $collection->count());
//        $entitkaNew = new EntitkaForEntityCollectionTest();  // bezidentity
//        $collection->set($entitkaNew);   //přidá entitu bez identity
//        $this->assertEquals(2, $collection->count());
//        $ret = $collection->getByIdentity($entitka8->getIdentity());  // vrací podle identity
//        $this->assertEquals($entitka8, $ret);
//        $collection->removeByIdentity($entitka8->getIdentity());  // odstraní podle identity
//        $this->assertEquals(1, $collection->count());        
//        $ret = $collection->getByIdentity($entitka8->getIdentity());   // vrací podle identity odstraněnou entitu
//        $this->assertEquals(NULL, $ret);
//        $collection->remove($entitkaNew);  // odstraní entitu
//        $this->assertEquals(0, $collection->count());
//    }    
//    public function testSetWithValidation() {
//        $validator = new IsTypeValidator('EntitkaForEntityCollectionTest');
//        $collection = new EntityCollection(NULL, $validator);        
//        $validEntity = new EntitkaForEntityCollectionTest();
//        $invalidObj = new ObjectForEntityCollectionTest();
//        $collection->set($validEntity); //přidá 
//        $this->assertEquals(1, $collection->count()); //jeden prvek kolekce
//        $collection->set($invalidObj); // nepřidá - nedělá nic
//        $this->assertEquals(1, $collection->count()); //jeden prvek kolekce
//    }
//    
//    public function testExchangeContentAndCountAndGetIterator() {
//        $collection = new EntityCollection(NULL, new IsTypeValidator('Pes\Entity\Persistable\PersistableEntityInterface'));
//        $source = array(
//            new EntitkaForEntityCollectionTest(),
//            new ObjectForEntityCollectionTest(),
//            new EntitkaForEntityCollectionTest());
//        $collection->mergeArrayContent($source);
//        $this->assertEquals('3', $collection->count());
//        foreach ($collection as $value) { // test get iterator
//            $ret[] = $value;
//        }
//        $this->assertEquals($source, $ret);
//    }
//    
//    public function testExchangeContentAndCountAndGetIteratorWithValidation () {
//        $validator = new IsTypeValidator('EntitkaForEntityCollectionTest');
//        $collection = new EntityCollection(NULL, $validator);  
//        $source = array(
//            new EntitkaForEntityCollectionTest(),
//            new ObjectForEntityCollectionTest(),
//            new EntitkaForEntityCollectionTest());
//        $collection->mergeArrayContent($source);
//        $this->assertEquals('2', $collection->count());
//        foreach ($collection as $value) { // test get iterator
//            $ret[] = $value;
//        }
//        $source[1] = $source[2];
//        unset ($source[2]);
//        $this->assertEquals($source, $ret);    }
//    
//    
//    public function testOrder() {
//        $source = array(
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('03')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('5'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX('2'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('aa'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('005')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('01')->setX('1'),
//        );
//        // Indexy pole $order nejsou jména vlastností (ty má entita private), ale jména metod (getterů). 
//        // Je třeba použít jméno bez závorek (např. "getA" nikoli "getA()").
//        $order = (new Order())->addOrdering('getA', OrderingEnum::DESCENDING)->addOrdering('getN', OrderingEnum::ASCENDING)->addOrdering('getX', OrderingEnum::DESCENDING);
//        $ordered = array(
//            (new EntitkaForEntityCollectionTest())->setA('005')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('03')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('aa'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('5'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX('2'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX('1'),
//        );
//        $collection = new EntityCollection(NULL, new IsTypeValidator('Pes\Entity\Persistable\PersistableEntityInterface'));
//        $collection->mergeArrayContent($source);
//        $orderComparator = new OrderComparatorClassMethods();
//        $collection->sort($orderComparator->getCompareFunction($order));
//        foreach ($collection as $key => $value) { 
//            // iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
//            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
//            $ret[] = $value;
//        }
//        $this->assertEquals($ordered, $ret);        
//    }
//
//    public function testSort() {
//        $source = array(
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('03')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('5'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX((new EntitkaForEntityCollectionTest())->setA('blx')->setN('ble')->setX('bli')),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('aa'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX((new EntitkaForEntityCollectionTest())->setA('bla')->setN('ble')->setX('bli')),
//            (new EntitkaForEntityCollectionTest())->setA('005')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('01')->setX('1'),
//        );
//        // Indexy pole $order nejsou jména vlastností (ty má entita private), ale jména metod (getterů). 
//        // Je třeba použít jméno bez závorek (např. "getA" nikoli "getA()").
//        $order = (new Order())->addOrdering('getA', OrderingEnum::DESCENDING)->addOrdering('getN', OrderingEnum::ASCENDING)->addOrdering('getX', OrderingEnum::DESCENDING);
//        $ordered = array(
//            (new EntitkaForEntityCollectionTest())->setA('005')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('004')->setN('03')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('aa'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('5'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('003')->setN('02')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('01')->setX('1'),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX((new EntitkaForEntityCollectionTest())->setA('blx')->setN('ble')->setX('bli')),
//            (new EntitkaForEntityCollectionTest())->setA('001')->setN('02')->setX((new EntitkaForEntityCollectionTest())->setA('bla')->setN('ble')->setX('bli')),
//        );        
//        $collection = new EntityCollection(NULL, new IsTypeValidator('Pes\Entity\Persistable\PersistableEntityInterface'));
//        $collection->mergeArrayContent($source);
//        $sortComparator = new SortComparatorClassMethods();
//        $collection->sort($sortComparator->getCompareFunction($order));
//        foreach ($collection as $key => $value) { 
//// iterator iteruje v ordered pořadí, ALE hodnoty $key obsahují původní indexy, se kterými byli členové kolekce setováni
//            // nefunguje tedy $ret[$key] = $value - takové pole se nerovná ordered
//            $ret[] = $value;
//        }
//        $this->assertEquals($ordered, $ret);        
//    }    
}
