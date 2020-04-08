<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use PHPUnit\Framework\TestCase;

use Pes\Security\Coder\Coder;
use Pes\Security\Coder\EnumEncoding;

/**
 * Description of CoderTest
 *
 * @author pes2704
 */
class CoderTest extends TestCase {
    public function testBase64Url() {
        $encoded = (new Coder(EnumEncoding::BASE64URL))->encode('ěščřžýáíéééééqwertzuiop..?..>');
        $this->assertEquals("xJvFocSNxZnFvsO9w6HDrcOpw6nDqcOpw6lxd2VydHp1aW9wLi4_Li4-", $encoded);
        $decoded = (new Coder(EnumEncoding::BASE64URL))->decode($encoded);
        $this->assertEquals('ěščřžýáíéééééqwertzuiop..?..>', $decoded);
    }

    public function testBase64() {
        $encoded = (new Coder(EnumEncoding::BASE64))->encode('ěščřžýáíéééééqwertzuiop..?..>');
        $this->assertEquals("xJvFocSNxZnFvsO9w6HDrcOpw6nDqcOpw6lxd2VydHp1aW9wLi4/Li4+", $encoded);
        $decoded = (new Coder(EnumEncoding::BASE64))->decode($encoded);
        $this->assertEquals('ěščřžýáíéééééqwertzuiop..?..>', $decoded);
    }

    public function testHex() {
        $encoded = (new Coder(EnumEncoding::HEX))->encode('ěščřžýáíéééééqwertzuiop');
        $this->assertEquals('c49bc5a1c48dc599c5bec3bdc3a1c3adc3a9c3a9c3a9c3a9c3a971776572747a75696f70', $encoded);
        $decoded = (new Coder(EnumEncoding::HEX))->decode($encoded);
        $this->assertEquals('ěščřžýáíéééééqwertzuiop', $decoded);
    }

    public function testRaw() {
        $encoded = (new Coder(EnumEncoding::RAW))->encode('ěščřžýáíéééééqwertzuiop');
        $this->assertEquals('ěščřžýáíéééééqwertzuiop', $encoded);
        $decoded = (new Coder(EnumEncoding::RAW))->decode($encoded);
        $this->assertEquals('ěščřžýáíéééééqwertzuiop', $decoded);
    }

    public function testAllCodingsInEnum() {
        foreach ((new EnumEncoding)->getConstList() as $enumValue) {
        $decoded = (new Coder($enumValue))->decode((new Coder($enumValue))->encode('ěščřžýáíéééééqwertzuiop..?..>'));
        $this->assertEquals('ěščřžýáíéééééqwertzuiop..?..>', $decoded);
        }
    }
}
