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
    public function adoptConfigurationParams(AssemblyInterface $params);
    public function getHost(): ?Host;
    public function getSmtp(): ?SmtpConnection;
    public function getParty(): ?Party;
    public function getContent(): ?Content;
    public function getHeaders(): ?Headers;
    public function setHost(Host $host): AssemblyInterface;
    public function setSmtp(SmtpConnection $smtpAuth): AssemblyInterface;
    public function setParty(Party $party): AssemblyInterface;
    public function setContent(Content $content): AssemblyInterface;
    public function setHeaders(Headers $headers): AssemblyInterface;
    
}
