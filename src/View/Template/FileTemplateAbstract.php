<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Template;
use Pes\View\Template\Exception\NoTemplateFileException;
/**
 * PhpTemplate.
 *
 * @author pes2704
 */
abstract class FileTemplateAbstract implements FileTemplateInterface {

    private $templateFileName;

    /**
     * Konstruktor - název souboru s template - název souboru včetně přípony.
     *
     * @param type $path
     * @throws NoTemplateFileException Pokud soubor neexistuje nebo ho nelze číst
     */
    public function __construct($path=null) {
        if(isset($path)) {
            $this->setTemplateFilename($path);
        }
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param string $path
     * @throws NoTemplateFileException Pokud soubor neexistuje nebo ho nelze číst
     */
    public function setTemplateFilename(string $path) {
        // is_readable - volá interní php funkci stat() a ta cachuje výsledek volání - manuál:     Note: The results of this function are cached. See clearstatcache() for more details.
        if (!is_readable($path)) {
            throw new NoTemplateFileException('Neexistuje nebo není čitelný soubor '.$path.'.');
        }
        $this->templateFileName = $path;
    }
    /**
     * Vrací název souboru s template.
     * @return string Název souboru s template
     */
    public function getTemplateFilename() {
        return $this->templateFileName;
    }

    /**
     * Vrací obsah souboru template jako string. Pokud soubor neexistuje, vrací prázdný řetězec.
     * @return string
     */
    public function getTemplateString() {
        $text = \file_get_contents($this->templateFileName);   //250 mikrosec (file_get_contents vrací FALSE při neúspěchu a E_WARNING, pokud neex soubor)
        return $text ? $text : '';
    }
}
