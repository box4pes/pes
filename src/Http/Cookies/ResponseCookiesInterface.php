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

use Psr\Http\Message\ResponseInterface;
use Pes\Http\Cookies\ResponseCookieInterface;

/**
 *
 * @author pes2704
 */
interface ResponseCookiesInterface {

    /**
     *
     * @param array $settings
     * @return $this
     */
    public function setDefaults(array $settings);

    /**
     *
     * @param ResponseCookieInterface $responseCookie
     * @return $this
     */
    public function setResponseCookie(ResponseCookieInterface $responseCookie);

    /**
     *
     * @param ResponseInterface $response
     * @return ResponseInterface Vrací response s doplněnou hlavičkou Set-Cookie
     */
    public function hydrateResponseRHeaders(ResponseInterface $response);

}
