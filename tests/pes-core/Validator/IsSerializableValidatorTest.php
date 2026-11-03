<?php

namespace Pes\Core\Validator;

use PHPUnit\Framework\TestCase;

use Pes\Core\Validator\IsSerializableValidator;
use Pes\Core\Validator\Exception\NotSerialisableException;

class SerializableClassForTest {
    public function __serialize() {
        return ['to_jsou_data' => 'To je série!'];
    }
    public function __unserialize(array $data) {
        return;
    }
}
/**
 * Description of IndexedCollectionTest
 *
 * @author pes2704
 */
class IsSerializableValidatorTest extends TestCase {

    public function testIsValid() {
        $validator = new IsSerializableValidator();
        $this->assertNull($validator->validate('asdfghjkl'));
        $this->assertNull($validator->validate(321321));
        $this->assertNull($validator->validate([1,2,3,4]));
        $this->assertNull($validator->validate(FALSE));
        $this->assertNull($validator->validate(NULL));
        $this->assertNull($validator->validate(new SerializableClassForTest()));
    }

    public function testNotSerialisableException() {
        $this->expectException(NotSerialisableException::class);
        $validator = new IsSerializableValidator();
        $validator->validate(new \stdClass());
    }
}
