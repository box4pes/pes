<?php

namespace Pes\Mail\Assembly;

/**
 * Description of Party
 *
 * @author pes2704
 */
class Party {
    private array $from = [];
    private array $to = [];
    private array $cc = [];
    private array $bcc = [];
    private array $replyTo = [];

    /**
     * Vrací pole obsahující dvě položky typu string: mail adresu odesilatele a zobrazované jméno odesílatele.
     * 
     * @return array
     */
    public function getFromArray(): array {
        return $this->encodeNames($this->from);
    }

    /**
     * Vrací pole příjemců, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getToArray(): array {
        return $this->encodeNames($this->to);
    }

    /**
     * Vrací pole příjemců kopie mailu, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getCcArray(): array {
        return $this->encodeNames($this->cc);
    }

    /**
     * Vrací pole příjemců skryté kopie mailu, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getBccArray(): array {
        return $this->encodeNames($this->bcc);
    }

    /**
     * Vrací pole příjemců odpovědi na mail, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getReplyToArray(): array {
        return $this->encodeNames($this->replyTo);
    }

    /**
     * Nastavení odesílatele.
     * 
     * @param string $fromAddress Mail adresa odesilatele
     * @param string $fromName Zobrazované jméno odesílatele
     * @return self
     */
    public function setFrom(string $fromAddress, string $fromName): self {
        $this->from = [$fromAddress, $fromName];
        return $this;
    }

    /**
     * Přidá příjemce mailu.
     * 
     * @param string $toAddress Mail adresa příjemce
     * @param string $toName Zobrazované jméno příjemce
     * @return self
     */
    public function addTo(string $toAddress, string $toName = ''): self {
        $this->to[] = [$toAddress, $toName];
        return $this;
    }

    /**
     * Přidá příjemce kopie mailu.
     * 
     * @param string $ccAddress Mail adresa příjemce kopie mailu
     * @param string $ccName Zobrazované jméno příjemce kopie mailu
     * @return self
     */
    public function addCc(string $ccAddress, string $ccName = ''): self {
        $this->cc[] = [$ccAddress, $ccName];
        return $this;
    }

    /**
     * 
     * @param string $bccAddress Mail adresa příjemce skryté kopie mailu
     * @param string $bccName Zobrazované jméno příjemce skryté kopie mailu
     * @return self
     */
    public function addBcc(string $bccAddress, string $bccName = ''): self {
        $this->bcc[] =  [$bccAddress, $bccName];
        return $this;
    }

    /**
     * Přidá příjemce odpovědi na mail.
     * 
     * @param string $replyToAddress Mail adresa příjemce odpovědi na mail
     * @param string $replyToName Zobrazované jméno příjemce odpovědi na mail
     * @return self
     */
    public function addReplyTo(string $replyToAddress, string $replyToName): self {
        $this->replyTo[] = [$replyToAddress, $replyToName];
        return $this;
    }

    private function encodeNames(array $addressArray) {
        return $addressArray;
        $addr = [];
        foreach ($addressArray as $address) {
            $addr[] = [$address[0], '=?utf-8?B?'.base64_encode($address[1])];
        }
    }
}
