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
    private array $headers = [];

    public function getHeader(string $name): array {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        } else {
            return [];
        }
    }

    public function getHeaderLine(string $name): string {
        if (array_key_exists($name, $this->headers)) {
            return implode(', ', $this->headers[$name]);
        } else {
            return '';
        }
    }

    public function getHeaders(): array {
        return $this->headers;
    }


    public function addHeader(string $name, string|array $directive): self {
        if (is_array($directive)) {
            $this->headers[$name] = $this->headers + [$name, $directive];
        } else {
            $this->headers[$name] = $this->headers + [$name, [$directive]];
        }
        return $this;
    }

    public function setHeaders(array $headers): self {
        $this->headers = $headers;
        return $this;
    }


}
