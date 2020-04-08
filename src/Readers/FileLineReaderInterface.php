<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

use Pes\Readers\FileInfo\FileInfoInterface;

/**
 *
 * @author pes2704
 */
interface FileLineReaderInterface {

    public function load(FileInfoInterface $fileInfo);
    public function getLine();
    public function close();
}
