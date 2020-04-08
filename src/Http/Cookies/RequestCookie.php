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
 * Description of RequestCookie
 *
 * @author pes2704
 */
class RequestCookie implements RequestCookieInterface {

    private $name;
    private $value;

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @param string $name
     * @return \Pes\Http\Cookies\RequestCookieInterface
     */
    public function setName($name): RequestCookieInterface {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     *
     * @param string $value
     * @return \Pes\Http\Cookies\RequestCookieInterface
     */
    public function setValue($value): RequestCookieInterface {
        $this->value = $value;
        return $this;
    }
}
