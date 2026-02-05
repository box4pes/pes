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
 * Description of ResponseStatus
 *
 * @author pes2704
 */
class ResponseStatus implements ResponseStatusInterface {

    #[\Override]
    public function isEmpty(ResponseInterface $response): bool
    {
        return in_array($response->getStatusCode(), [204, 205, 304]);
    }

    public function isInformational(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 100 && $response->getStatusCode() < 200;
    }

    public function isOk(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 200;
    }

    #[\Override]
    public function isSuccessful(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }


    #[\Override]
    public function isRedirect(ResponseInterface $response): bool
    {
        return in_array($response->getStatusCode(), [301, 302, 303, 307]);
    }

    #[\Override]
    public function isRedirection(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 300 && $response->getStatusCode() < 400;
    }

    #[\Override]
    public function isForbidden(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 403;
    }

    #[\Override]
    public function isNotFound(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 404;
    }

    #[\Override]
    public function isClientError(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 400 && $response->getStatusCode() < 500;
    }

    #[\Override]
    public function isServerError(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 500 && $response->getStatusCode() < 600;
    }
}
