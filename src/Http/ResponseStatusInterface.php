<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http;

use Psr\Http\Message\ResponseInterface;

/**
 *
 * @author pes2704
 */
interface ResponseStatusInterface {

    /**
     * Is this response empty?
     *
     * @return bool
     */
    public function isEmpty(ResponseInterface $response);

    /**
     * Is this response informational?
     *
     * @return bool
     */
    public function isInformational(ResponseInterface $response);

    /**
     * Is this response OK?
     *
     * @return bool
     */
    public function isOk(ResponseInterface $response);

    /**
     * Is this response successful?
     *
     * @return bool
     */
    public function isSuccessful(ResponseInterface $response);

    /**
     * Is this response a redirect?
     *
     * @return bool
     */
    public function isRedirect(ResponseInterface $response);

    /**
     * Is this response a redirection?
     *
     * @return bool
     */
    public function isRedirection(ResponseInterface $response);

    /**
     * Is this response forbidden?
     *
     * @return bool
     * @api
     */
    public function isForbidden(ResponseInterface $response);

    /**
     * Is this response not Found?
     *
     * @return bool
     */
    public function isNotFound(ResponseInterface $response);

    /**
     * Is this response a client error?
     *
     * @return bool
     */
    public function isClientError(ResponseInterface $response);
    
    /**
     * Is this response a server error?
     *
     * @return bool
     */
    public function isServerError();
}
