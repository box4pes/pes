<?php
use PHPUnit\Framework\TestCase;

use Pes\Router\UrlPatternValidator;
use Pes\Router\Exception\WrongPatternFormatException;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of testUrlPatternValidator
 *
 * @author pes2704
 */
class UrlPatternValidatorTest extends TestCase {

    private $validator;

    protected function setUp(): void {
        $this->validator = new UrlPatternValidator();
    }
    public function testValidate() {
        $this->validator->validate('/');
        $this->validator->validate('/kuk/');
        $this->validator->validate('/kuk/:id/');
        $this->assertTrue(true, "Validátor nevyhodil výjimku pro korektní pattern.");
    }

    public function testExceptionEmptyPattern() {
        $this->expectException(WrongPatternFormatException::class);
        $this->validator->validate('');
    }

    public function testExceptionMissingLeftSlash() {
        $this->expectException(WrongPatternFormatException::class);
        $this->validator->validate('kuk/');
    }

    public function testExceptionParemeterInFirstSection() {
        $this->expectException(WrongPatternFormatException::class);
        $this->validator->validate('/:id/');
    }
}
