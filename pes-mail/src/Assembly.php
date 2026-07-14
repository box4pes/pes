<?php

namespace Pes\Mail;

use Pes\Mail\AssemblyInterface;
use Pes\Mail\Assembly\Host;
use Pes\Mail\Assembly\Encryption;
use Pes\Mail\Assembly\SmtpConnection;
use Pes\Mail\Assembly\Party;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Attachment;
use Pes\Mail\Assembly\Headers;


/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Assembly implements AssemblyInterface {

    /**
     *
     * @var Host
     */
    private $host;

    /**
     *
     * @var SmtpConnection
     */
    private $smtp;

    /**
     *
     * @var Encryption
     */
    private $encryption;

    /**
     *
     * @var Party
     */
    private $party;

    /**
     *
     * @var Content
     */
    private $content;

    /**
     *
     * @var Headers
     */
    private $headers;

    public function adoptConfigurationParams(AssemblyInterface $params) {
        if ($params->getContent()) {
            $this->setContent($params->getContent());
        }
        if ($params->getHeaders()) {
            $this->setHeaders($params->getHeaders());
        }
        if ($params->getHost()) {
            $this->setHost($params->getHost());
        }
        if ($params->getParty()) {
            $this->setParty($params->getParty());
        }
        if ($params->getSmtp()) {
            $this->setSmtp($params->getSmtp());
        }

    }

    public function getHost(): ?Host {
        return $this->host;
    }

    public function getSmtp(): ?SmtpConnection {
        return $this->smtp;
    }

    public function getParty(): ?Party {
        return $this->party;
    }

    public function getContent(): ?Content {
        return $this->content;
    }

    public function getHeaders(): ?Headers {
        return $this->headers;
    }

    public function setHost(Host $host): AssemblyInterface {
        $this->host = $host;
        return $this;
    }

    public function setSmtp(SmtpConnection $smtp): AssemblyInterface {
        $this->smtp = $smtp;
        return $this;
    }

    public function setParty(Party $party): AssemblyInterface {
        $this->party = $party;
        return $this;
    }

    public function setContent(Content $content): AssemblyInterface {
        $this->content = $content;
        return $this;
    }

    public function setHeaders(Headers $headers): AssemblyInterface {
        $this->headers = $headers;
        return $this;
    }

}
