<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

/**
 * Description of StringLineReader
 *
 * @author pes2704
 */
class StringLineFileReader extends LineFileReaderAbstract {
    protected function getLineSpecial() {
        return fgets($this->handler);
    }
}
