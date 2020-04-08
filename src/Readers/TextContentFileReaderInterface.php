<?php
namespace Pes\Readers;

use Pes\Readers\FileReaderInterface;

/**
 *
 * @author pes2704
 */
interface TextContentReaderInterface extends FileReaderInterface{

    public function getDataInUtf8();

    /**
     * Reader který vrací textový obsah (Content-Type např. text/html, text/plain, tedy všechny typy text) musí vracet
     * kódování obsahu (vraceného metodou getData().
     */
    public function getEncoding();
}
