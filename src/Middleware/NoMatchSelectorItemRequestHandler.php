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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;


use Pes\Http\Factory\ResponseFactory;


/**
 * Description of NonprocessedRequestHandler
 *
 * @author pes2704
 */
class NoMatchSelectorItemRequestHandler extends RequestHandler {

    public function __construct(
            LoggerInterface $logger = null
    ) {
        if(isset($logger)) {
            parent::__construct(
                function (ServerRequestInterface $request) use ($logger) {
                $logger->warning("Nenalezen selector item pro request s uri path: '".$request->getUri()->getPath()."'");
                $response = (new ResponseFactory())->createResponse();
                ####  body  ####
                $size = $response->getBody()->write("404 Not Found");
                $response->getBody()->rewind();
                return $response;
                
                
                }
                );
        } else {
            parent::__construct(
                function (ServerRequestInterface $request) {throw new \LogicException("Nenalezen selector item pro request s uri path: '".$request->getUri()->getPath()."'");}
                );
        }
    }
}
