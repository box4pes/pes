<?php
namespace Pes\Mail;

use Pes\Mail\Assembly\Host;
use Pes\Mail\Assembly\Encryption;
use Pes\Mail\Assembly\SmtpConnection;
use Pes\Mail\Assembly\Party;
use Pes\Mail\Assembly\Content;
use Pes\Mail\Assembly\Attachment;
use Pes\Mail\Assembly\Headers;


/**
 *
 * @author vlse2610
 */
interface AssemblyInterface {
    public function adoptConfigurationParams(AssemblyInterface $params): void;
    public function getHost(): ?Host;
    public function getSmtp(): ?SmtpConnection;
    public function getParty(): ?Party;
    public function getContent(): ?Content;
    public function getHeaders(): ?Headers;
    public function setHost(Host $host): self;
    public function setSmtp(SmtpConnection $smtpAuth): self;
    public function setParty(Party $party): self;
    public function setContent(Content $content): self;
    public function setHeaders(Headers $headers): self;
    
}
