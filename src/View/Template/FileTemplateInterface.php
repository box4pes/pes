<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\View\Template;

/**
 *
 * @author pes2704
 */
interface FileTemplateInterface extends TemplateInterface {

    /**
     * Vrací název souboru s template.
     * @return string Název souboru s template
     */
    public function getTemplateFilename();

    /**
     * Vrací obsah souboru template jako string. Pokud soubor neexistuje, vrací prázdný řetězec.
     * @return string
     */
    public function getTemplateString();

}
