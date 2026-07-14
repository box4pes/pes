<?php

namespace Pes\Mail\Assembly;

use Pes\Mail\Assembly\Attachment;

/**
 * Description of Content
 *
 * @author pes2704
 */
class Content {

    private $subject;

    private $html;

    private $attachments = [];

    /**
     * Vrací předmět mailu v kódování vhodném pro přenost e-mailenm a e-mail klienty. V e-mail klientovi je tento text správně automaticky dekódován a zobrazen.
     * Text je je zakódován pomocí base64 a následně předán s informací, že se jadná o text ve znakové sadě UTF-8 a kódovaný base64.
     * 
     * @return string
     */
    public function getSubjectBase64() : string {
        return '=?utf-8?B?'.base64_encode($this->subject).'?=';
    }
    
    /**
     * Vrací předmět e-mailu bez kódování.
     * 
     * @return string
     */
    public function getSubjectRaw(): string {
        return $this->subject;
    }

    public function getHtml(): string {
        return $this->html;
    }

    public function getAltBody() {
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        return "Content without HTML: ".wordwrap(strip_tags($this->html), 70, PHP_EOL);
    }

    /**
     *
     * @return Attachment iterable
     */
    public function getAttachments(): iterable {
        return $this->attachments;
    }

    public function setSubject(string $subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setHtml(string $html) {
        $this->html = $html;
        return $this;
    }

    public function setAttachments(iterable $attachments) {
        $this->attachments = $attachments;
        return $this;
    }


}
