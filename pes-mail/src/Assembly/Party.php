<?php

namespace Pes\Mail\Assembly;

/**
 * Description of Party
 *
 * @author pes2704
 */
class Party {
    private $from;
    private $to;
    private $cc;
    private $bcc;
    private $replyTo;

    /**
     * Vrací pole obsahující dvě položky typu string: mail adresu odesilatele a zobrazované jméno odesílatele.
     * 
     * @return array
     */
    public function getFrom() {
        return $this->encodeNames($this->from);
    }

    /**
     * Vrací pole příjemců, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getToArray() {
        return $this->encodeNames($this->to);
    }

    /**
     * Vrací pole příjemců kopie mailu, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getCcArray() {
        return $this->encodeNames($this->cc);
    }

    /**
     * Vrací pole příjemců skryté kopie mailu, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getBccArray() {
        return $this->encodeNames($this->bcc);
    }

    /**
     * Vrací pole příjemců odpovědi na mail, každá položka příjemce je pole obsahujících dvě položky typu string: mail adresu příjemce a zobrazované jméno příjemce.
     * 
     * @return array
     */
    public function getReplyTo() {
        return $this->encodeNames($this->replyTo);
    }

    /**
     * Nastavení odesílatele.
     * 
     * @param string $fromAddress Mail adresa odesilatele
     * @param string $fromName Zobrazované jméno odesílatele
     * @return $this
     */
    public function setFrom($fromAddress, $fromName) {
        $this->from = [$fromAddress, $fromName];
        return $this;
    }

    /**
     * Přidá příjemce mailu.
     * 
     * @param string $toAddress Mail adresa příjemce
     * @param string $toName Zobrazované jméno příjemce
     * @return $this
     */
    public function addTo($toAddress, $toName = '') {
        $this->to[] = [$toAddress, $toName];
        return $this;
    }

    /**
     * Přidá příjemce kopie mailu.
     * 
     * @param string $ccAddress Mail adresa příjemce kopie mailu
     * @param string $ccName Zobrazované jméno příjemce kopie mailu
     * @return $this
     */
    public function addCc($ccAddress, $ccName = '') {
        $this->cc[] = [$ccAddress, $ccName];
        return $this;
    }

    /**
     * 
     * @param string $bccAddress Mail adresa příjemce skryté kopie mailu
     * @param string $bccName Zobrazované jméno příjemce skryté kopie mailu
     * @return $this
     */
    public function addBcc($bccAddress, $bccName = '') {
        $this->bcc[] =  [$bccAddress, $bccName];
        return $this;
    }

    /**
     * Přidá příjemce odpovědi na mail.
     * 
     * @param type $replyToAddress Mail adresa příjemce odpovědi na mail
     * @param type $replyToName Zobrazované jméno příjemce odpovědi na  mail
     * @return $this
     */
    public function addReplyTo($replyToAddress, $replyToName) {
        $this->replyTo[] = [$replyToAddress, $replyToName];
        return $this;
    }

    private function encodeNames($addressArray) {
        return $addressArray;
        $addr = [];
        foreach ($addressArray as $address) {
            $addr[] = [$address[0], '=?utf-8?B?'.base64_encode($address[1])];
        }
    }
}
