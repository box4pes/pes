<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

use Pes\Http\Response;
use Pes\Http\Headers;
use Pes\Http\Body;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of ResponseFactory
 *
 * @author pes2704
 */
class ResponseFactory implements ResponseFactoryInterface {

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
        return new Response($code, new Headers(), new Body(fopen('php://temp', 'r+')), $reasonPhrase);
    }
}
