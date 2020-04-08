<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\MethodEnum;

/**
 * Test Pes\Type\ColumnAccessEnum
 *
 * @author pes2704
 */
class MethodEnumTest extends TestCase {

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetGetMethodType() {
        $type = new MethodEnum();
        $this->assertSame('GET', MethodEnum::GET);
        $this->assertSame('GET', $type(MethodEnum::GET));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetPostMethodType() {
        $type = new MethodEnum();
        $this->assertSame('POST', MethodEnum::POST);
        $this->assertSame('POST', $type(MethodEnum::POST                                                                                                                                                                                                                           ));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetPutMethodType() {
        $type = new MethodEnum();
        $this->assertSame('PUT', MethodEnum::PUT);
        $this->assertSame('PUT', $type(MethodEnum::PUT));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetDeleteMethodType() {
        $type = new MethodEnum();
        $this->assertSame('DELETE', MethodEnum::DELETE);
        $this->assertSame('DELETE', $type(MethodEnum::DELETE));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetOptionMethodType() {
        $type = new MethodEnum();
        $this->assertSame('OPTIONS', MethodEnum::OPTIONS);
        $this->assertSame('OPTIONS', $type(MethodEnum::OPTIONS));
    }

    /**
     * existence konstanty
     * zda hodnota konstanty je enum typu
     */
    public function testGetPatchMethodType() {
        $type = new MethodEnum();
        $this->assertSame('PATCH', MethodEnum::PATCH);
        $this->assertSame('PATCH', $type(MethodEnum::PATCH));
    }
}
