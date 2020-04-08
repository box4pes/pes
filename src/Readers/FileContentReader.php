<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

/**
 * Description of FileConzentReader
 *
 * @author pes2704
 */
class FileContentReader extends FileReaderAbstract implements FileContentReaderInterface {

   /**
    * Načte obsah souboru proměnné a vrací ho jako string. Pokud bylo povoleno užívaní cache, vrací prioritně obsah cache.
    * @return string Načtený obsah souboru
    */
    public function getData() {
        return file_get_contents($this->jmenoSouboruSCestou);
    }

}
