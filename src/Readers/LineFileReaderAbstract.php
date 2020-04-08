<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Readers;

/**
 * Description of FileLineReader
 *
 * @author pes2704
 */
class LineFileReaderAbstract extends FileReaderAbstract implements FileLineReaderInterface, \Iterator {


    /**
     * @var resource
     */
    protected $handler;
    protected $closed;
    protected $position = 0;

    /**
     * Předčasně načtení řádka při volání valid()
     * @var type
     */
    protected $prereadLine=false;


    public function load(FileInfo\FileInfoInterface $fileInfo) {
        parent::load($fileInfo);
        $this->init();
    }

    public function init() {
        $this->close();
        $this->handler = fopen($this->fileInfo->getFullFileName(), 'r');  // Open for reading only; place the file pointer at the beginning of the file.
        $this->position = 0;
        $this->closed = false;
    }

    protected function getLineSpecial() {
        throw new LogicException("Child class must implement own getLineSpecial() method.");
    }

    public function getLine() {
        if (!$this->closed) {
            if (!$this->prereadLine) {
                $line = $this->getLineSpecial();
            } else {
                $line = $this->prereadLine;
                $this->prereadLine = false;
            }
            if ($line) {
                return $line;
            } else {
                $this->close();
            }
        }
    }

    public function close() {
        if (is_resource($this->handler)) {
            fclose($this->handler);
        }
        $this->prereadLine = false;
        $this->closed = true;
    }
    public function __destruct() {
        $this->close();
    }

    ## iterator

    public function rewind() {
        $this->close();
        $this->init();
    }

    public function current() {
        if ($this->prereadLine) {
            $line = $this->prereadLine;
            $this->prereadLine = false;
        } else {
            $line = $this->getLine();
        }
        return $line;
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        $this->prereadLine = $this->getLine();  // posune handler
        if ($this->prereadLine) {
            ++$this->position;  // není konec souboru
        }
    }

    public function valid() {
        if (!$this->prereadLine) {
            $this->prereadLine = $this->getLine();
        }
        return $this->prereadLine ? true : false;
    }
}
