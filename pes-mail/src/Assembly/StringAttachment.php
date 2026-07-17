<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Mail\Assembly;

/**
 * Description of StringAttachment
 *
 * @author pes2704
 */
class StringAttachment {


    private string $stringAttachment;
    private string $altText;

    public function getStringAttachment(): string {
        return $this->stringAttachment;
    }

    public function getAltText(): string {
        return $this->altText;
    }

    public function setStringAttachment($stringAttachment): self {
        $this->stringAttachment = $stringAttachment;
        return $this;
    }

    public function setAltText($altText): self {
        $this->altText = $altText;
        return $this;
    }



}
