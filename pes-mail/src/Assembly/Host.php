<?php

namespace Pes\Mail\Assembly;

/**
 * Description of Host
 *
 * @author pes2704
 */
class Host {

    private $host;

    public function getHost(): string {
        return $this->host;
    }

    public function setHost(string $host) {
        $this->host = $host;
        return $this;
    }
}
