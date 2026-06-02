<?php

namespace Pes\Core\Validator;

use PHPUnit\Framework\TestCase;

use Pes\Core\Validator\IsObjectTypeValidator;
use Pes\Core\Validator\Exception\NotValidTypeException;
use Pes\Core\Validator\Exception\TypeNameNotAStringException;
use Pes\Core\Validator\Exception\TypeNotExistsException;


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
     * @expectedException Pes\Core\Validator\Exception\TypeNotExistsException
     */
    public function testTypeNotExistsException() {
            $validator = new IsObjectTypeValidator('Blabla');
    }
    /**
     * @expectedException Pes\Core\Validator\Exception\TypeNameNotAStringException
     */
    public function testTypeNameNotAStringException() {
        $validator = new IsObjectTypeValidator(188);
    }

    public function testCorrectConstruct() {
        $this->assertInstanceOf(IsObjectTypeValidator::class, new IsObjectTypeValidator(InterfaceForIsTypeValidatorTest::class));
        $this->assertInstanceOf(IsObjectTypeValidator::class, new IsObjectTypeValidator(ObjectForIsTypeValidatorTest::class));
    }

    /**
     *
     */
    public function testIsValidClass() {
        $validator = new IsObjectTypeValidator(ObjectForIsTypeValidatorTest::class);
        $this->assertNull($validator->validate(new ObjectForIsTypeValidatorTest()));
    }

    public function testIsValidInterface() {
        $validator = new IsObjectTypeValidator(InterfaceForIsTypeValidatorTest::class);
        $this->assertNull($validator->validate(new InterfacedObjectForIsTypeValidatorTest()));
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotValidTypeException
     */
    public function testInvalidInterface() {
        $validator = new IsObjectTypeValidator(InterfaceForIsTypeValidatorTest::class);
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotValidTypeException
     */
    public function testInvalidClass() {
        $validator = new IsObjectTypeValidator(ObjectForIsTypeValidatorTest::class);
        $validator->validate(new AnotherObjectForIsTypeValidatorTest());
    }

}
