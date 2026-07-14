<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Mail\Assembly;

/**
 * Description of Headers
 *
 * @author pes2704
 */
class Headers {
    private $headers = [];

    public function getHeader($name): array {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        } else {
            return [];
        }
    }

    public function getHeaderLine($name) {
        if (array_key_exists($name, $this->headers)) {
            return implode(', ', $this->headers[$name]);
        } else {
            return '';
        }
    }

    public function getHeaders(): array {
        return $this->headers;
    }


    public function addHeader($name, $directive) {
        if (is_array($directive)) {
            $this->headers[$name] = $this->headers + [$name, $directive];
        } else {
            $this->headers[$name] = $this->headers + [$name, [$directive]];
        }
    }

    public function setHeaders(array $headers) {
        $this->headers = $headers;
        return $this;
    }


}
