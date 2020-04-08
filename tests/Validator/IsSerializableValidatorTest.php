<?php
use PHPUnit\Framework\TestCase;

use Pes\Validator\IsSerializableValidator;

class SerializableClassForTest implements \Serializable {
    public function serialize() {
        return 'To je série!';
    }
    public function unserialize($serialized) {
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
        $validator->validate('asdfghjkl');
        $validator->validate(321321);
        $validator->validate([1,2,3,4]);
        $validator->validate(FALSE);
        $validator->validate(NULL);
        $validator->validate(new stdClass());
        $validator->validate(new SerializableClassForTest());
    }
}
