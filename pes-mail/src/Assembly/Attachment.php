<?php

namespace Pes\Mail\Assembly;

/**
 * Description of Attachment
 *
 * @author pes2704
 */
class Attachment {

    private $fileName;
    private $altText;

    public function getFileName(): string {
        return $this->fileName;
    }

    public function getAltText(): string {
        return $this->altText;
    }

    public function setFileName(string $fileName) {
        $this->fileName = $fileName;
        return $this;
    }

    public function setAltText(string $altText) {
        $this->altText = $altText;
        return $this;
    }


}
