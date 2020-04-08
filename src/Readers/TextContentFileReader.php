<?php
namespace Pes\Readers;

use Pes\Readers\TextReaderInterface;

/**
 * Description of TextFileReader
 *
 * @author pes2704
 */
class TextFileReader extends FileContentReader implements TextContentReaderInterface {

    private $encoding;

    public function __construct( $encoding='UTF-8') {
        $this->encoding = $encoding;
    }

    public function load(FileInfo\FileInfoInterface $fileInfo) {
        parent::load($fileInfo);
        $typeParts = explode('/', $this->fileInfo->getContentType());
        if (!strpos('text', $typeParts[0]) === 0) {
            throw new InvalidArgumentException('Reader je pro obsah typu text a rozpoznaný MIME type (Content-Type) zadaného souboru není text, je '.$this->contentType);
        }
    }

    /**
     *
     * https://github.com/neitanod/forceutf8
     * http://stackoverflow.com/questions/910793/detect-encoding-and-make-everything-utf-8
     */
    public function getData() {
        $str = parent::getData();
        if (!\mb_detect_encoding($str, $this->encoding, true)) {
            throw new UnexpectedValueException('Přečtený obsah souboru není v zadaném kódování '.$this->encoding.'. Soubor '.$this->getBaseName());
        }
        return $str;
    }

    public function getDataInUtf8() {
        return mb_convert_encoding($this->getData(), "UTF-8", [$this->encoding]);
    }

    public function getEncoding() {
        return $this->encoding;
    }


}
