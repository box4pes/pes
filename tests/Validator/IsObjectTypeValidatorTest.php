<?php
use PHPUnit\Framework\TestCase;

use Pes\Validator\IsObjectTypeValidator;
use Pes\Validator\Exception\NotValidTypeException;
use Pes\Validator\Exception\TypeNameNotAStringException;
use Pes\Validator\Exception\TypeNotExistsException;


interface InterfaceForIsTypeValidatorTest {}

class ObjectForIsTypeValidatorTest {}

class InterfacedObjectForIsTypeValidatorTest implements InterfaceForIsTypeValidatorTest {}

class AnotherObjectForIsTypeValidatorTest {}

/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsObjectTypeValidatorTest extends TestCase {

    /**
     * @expectedException Pes\Validator\Exception\TypeNotExistsException
     */
    public function testTypeNotExistsException() {
            $validator = new IsObjectTypeValidator('Blabla');
    }
    /**
     * @expectedException Pes\Validator\Exception\TypeNameNotAStringException
     */
    public function testTypeNameNotAStringException() {
        $validator = new IsObjectTypeValidator(188);
    }

    public function testCorrectConstruct() {
        $this->assertInstanceOf(IsObjectTypeValidator::class, new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest'));
        $this->assertInstanceOf(IsObjectTypeValidator::class, new IsObjectTypeValidator('ObjectForIsTypeValidatorTest'));
    }

    /**
     *
     */
    public function testIsValidClass() {
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');
        $this->assertNull($validator->validate(new ObjectForIsTypeValidatorTest()));
    }

    public function testIsValidInterface() {
        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');
        $this->assertNull($validator->validate(new InterfacedObjectForIsTypeValidatorTest()));
    }

    /**
     * @expectedException Pes\Validator\Exception\NotValidTypeException
     */
    public function testInvalidInterface() {
        $validator = new IsObjectTypeValidator('InterfaceForIsTypeValidatorTest');
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

    /**
     * @expectedException Pes\Validator\Exception\NotValidTypeException
     */
    public function testInvalidClass() {
        $validator = new IsObjectTypeValidator('ObjectForIsTypeValidatorTest');
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

}
