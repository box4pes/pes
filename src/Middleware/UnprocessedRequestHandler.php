<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Description of NonprocessedRequestHandler
 *
 * @author pes2704
 */
class UnprocessedRequestHandler extends RequestHandler {
    public function __construct() {
        parent::__construct(
                function ( RequestInterface $request) {
                    throw new \LogicException("Nevyhodnocený request: řetézec middleware vyčerpán bez získání výstupu. method: {$request->getMethod()}, path: {$request->getUri()->getPath()}");

                }
            );
    }
}
