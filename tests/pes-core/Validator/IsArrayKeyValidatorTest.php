<?php

namespace Pes\Core\Validator;

use PHPUnit\Framework\TestCase;

use Pes\Core\Validator\IsArrayKeyValidator;

/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsArrayKeyValidatorTest extends TestCase {

    private $validator;

    public function setUp(): void {
        $this->validator = new IsArrayKeyValidator();
    }

    public function testIsValidString() {
        // klíč pole může být integer nebo string
        $this->assertNull($this->validator->validate('asdfghjkl'));
    }

    public function testIsValidInteger() {
        // klíč pole může být integer nebo string
        $this->assertNull($this->validator->validate(321321));
    }

    public function testIsValidEmptyString() {
        // klíč pole může být integer nebo string
        $this->assertNull($this->validator->validate(''));
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotArrayKeyException
     */
    public function testArray() {
        $this->validator->validate([654]);
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotArrayKeyException
     */
    public function testObject() {
        $this->validator->validate(new \stdClass());
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotArrayKeyException
     */
    public function testBool() {
        $this->validator->validate(FALSE);
    }

    /**
     * @expectedException Pes\Core\Validator\Exception\NotArrayKeyException
     */
    public function testNull() {
        $this->validator->validate(NULL);
    }
}
