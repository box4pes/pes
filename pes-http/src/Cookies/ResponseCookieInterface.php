<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Cookies;

/**
 *
 * @author pes2704
 */
interface ResponseCookieInterface {
    
    public function getName() ;

    public function setName($name);

    public function getValue();

    public function getAttributes() ;

    public function setValue($value=''): ResponseCookieInterface;

    public function setAttributes(array $attributes=[]): ResponseCookieInterface;
}
