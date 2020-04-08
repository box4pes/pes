<?php
namespace Pes\Readers\FileInfo;

/**
 * Description
 *
 * @author vlse2610
 */
/**
 * Objekt slouží ke čtení obsahu souboru.
 */
class FileInfo implements FileInfoInterface {

    protected $jmenoSouboruSCestou;
    protected $dirName;
    protected $baseFileName;
    protected $fileName;
    protected $extension;

    protected $contentType;
//----------------------------------------------------

    /**
     * Naplní ->jmenoSouboruSCestou.
     * @param string $jmenoSouboru úplné jméno souboru, např. 'D:/cesta/adresar/soubor.pripona
     */
    /**
     * PROTECTED Konstruktor. Zadáním parametru je možné povolit užívání cache pro obsah souboru.
     * @param type $jmenoSouboru
     * @param type $useMemoryCache
     * @throws InvalidArgumentException
     */
    public function __construct( $jmenoSouboru) {
        if (is_readable($jmenoSouboru)) {
            $this->jmenoSouboruSCestou  =  $jmenoSouboru;
            $path_parts = pathinfo($this->jmenoSouboruSCestou);
            $this->baseFileName = $path_parts['basename'];
            $this->dirName = $path_parts['dirname'];
            $this->fileName = $path_parts['filename'];
            $this->extension = $path_parts['extension'];
            $this->contentType = mime_content_type($jmenoSouboru);  //další info http://php.net/manual/en/function.mime-content-type.php
            if (!isset($this->contentType)) {
                throw new InvalidArgumentException('Nepodařilo se rozpoznat MIME type (Content-Type) zadaného souboru '.$jmenoSouboru);
            }
        } else {
            throw new \InvalidArgumentException('Nelze číst zadaný soubor '.$jmenoSouboru);
        }
    }

    public function getContentType() {
        return $this->contentType;
    }

    function getFullFileName() {
        return $this->jmenoSouboruSCestou;
    }

    public function getBaseName() {
        return $this->baseFileName;
    }

    function getDirName() {
        return $this->dirName;
    }

    function getFileName() {
        return $this->fileName;
    }

    function getExtension() {
        return $this->extension;
    }


}
