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
    
    private ?Host $host = null;

    private ?SmtpConnection $smtp = null;

    private ?Party $party = null;

    private ?Content $content = null;

    private ?Headers $headers = null;

    public function adoptConfigurationParams(AssemblyInterface $params): void {
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

    public function setHost(Host $host): self {
        $this->host = $host;
        return $this;
    }

    public function setSmtp(SmtpConnection $smtp): self {
        $this->smtp = $smtp;
        return $this;
    }

    public function setParty(Party $party): self {
        $this->party = $party;
        return $this;
    }

    public function setContent(Content $content): self {
        $this->content = $content;
        return $this;
    }

    public function setHeaders(Headers $headers): self {
        $this->headers = $headers;
        return $this;
    }

}
