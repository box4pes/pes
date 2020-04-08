<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

use Pes\Readers\FileInfo\FileInfoInterface;

/**
 * Description of FileReaderAbstracz
 *
 * @author pes2704
 */
abstract class FileReaderAbstract implements FileReaderInterface {

    /**
     * @var FileInfoInterface
     */
    protected $fileInfo;


    public function load(FileInfoInterface $fileInfo) {
        $this->fileInfo = $fileInfo;
    }


}
