<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Http;

use Psr\Http\Message\ResponseInterface;

/**
 *
 * @author pes2704
 */
interface ResponseSenderInterface {
    public function send(ResponseInterface $response);
}
