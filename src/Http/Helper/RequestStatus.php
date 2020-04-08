<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Helper;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of RequestStatus
 *
 * @author pes2704
 */
class RequestStatus {

    /**
     * Does this request use a given method?
     *
     * @param  string $method HTTP method
     * @return bool
     */
    private static function isMethod(ServerRequestInterface $request, $method)
    {
        return $request->getMethod() === $method;
    }

    /**
     * Is this a GET request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isGet(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'GET');
    }

    /**
     * Is this a POST request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isPost(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'POST');
    }

    /**
     * Is this a PUT request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isPut(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'PUT');
    }

    /**
     * Is this a PATCH request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isPatch(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'PATCH');
    }

    /**
     * Is this a DELETE request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isDelete(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'DELETE');
    }

    /**
     * Is this a HEAD request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isHead(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'HEAD');
    }

    /**
     * Is this a OPTIONS request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isOptions(ServerRequestInterface $request)
    {
        return self::isMethod($request, 'OPTIONS');
    }

    /**
     * Is this an XHR request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public static function isXhr(ServerRequestInterface $request)
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

}
