<?php
use PHPUnit\Framework\TestCase;

use Pes\Collection\MapCollection;
use Pes\Validator\IsObjectTypeValidator;
use Pes\Validator\Exception\NotValidTypeException;

interface InterfaceForIsTypeValidatorTest {

}

class ObjectForIsTypeValidatorTest {

}
class InterfacedObjectForIsTypeValidatorTest implements InterfaceForIsTypeValidatorTest {

}
class AnotherObjectForIsTypeValidatorTest {

}
/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsObjectTypeValidatorTest extends TestCase {

    public function testConstructor() {
        try {
            $validator = new IsObjectTypeValidator('Blabla');   // Vyhodí výjimku
        } catch (\InvalidArgumentException $uve) {
            $this->assertStringStartsWith('Nenalezen zadaný typ (interface nebo class)', $uve->getMessage());
        }
        try {
            $validator = new IsObjectTypeValidator(188);   // Vyhodí výjimku
        } catch (\InvalidArgumentException $uve) {
            $this->assertStringStartsWith('Jméno typu musí být zadáno jako string.', $uve->getMessage());
        }

        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');
    }

    /**
     *
     */
    public function testIsValidClass() {
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');
        $validator->validate(new ObjectForIsTypeValidatorTest());
    }

    public function testIsValidInterface() {
        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');
        $validator->validate(new InterfacedObjectForIsTypeValidatorTest());
    }

    /**
     * @expectedException NotValidTypeException
     */
    public function testInvalidInterface() {
        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

    /**
     * @expectedException NotValidTypeException
     */
    public function testInvalidClass() {
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

}
