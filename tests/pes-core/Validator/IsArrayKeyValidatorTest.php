<?php

namespace Pes\Core\Validator;

use PHPUnit\Framework\TestCase;

use Pes\Core\Validator\IsArrayKeyValidator;
use Pes\Core\Validator\Exception\NotArrayKeyException;

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

    public function testArray() {
        $this->expectException(NotArrayKeyException::class);
        $this->validator->validate([654]);
    }

    public function testObject() {
        $this->expectException(NotArrayKeyException::class);
        $this->validator->validate(new \stdClass());
    }

    public function testBool() {
        $this->expectException(NotArrayKeyException::class);
        $this->validator->validate(FALSE);
    }

    public function testNull() {
        $this->expectException(NotArrayKeyException::class);
        $this->validator->validate(NULL);
    }
}
