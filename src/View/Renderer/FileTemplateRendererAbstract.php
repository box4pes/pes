<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\View\Renderer;

use Pes\View\Template\FileTemplateInterface;
use Pes\View\Renderer\Exception\NoTemplateFileException;

/**
 * Description of FileTemplateRendererAbstract
 *
 * @author pes2704
 */
class FileTemplateRendererAbstract {


    /**
     *
     * @param FileTemplateInterface $fileTemplate
     * @return string|bool vrací FALSE při neúspěchu
     * @throws NoTemplateFileException Pokud soubor neexistuje nebo ho nelze číst
     */
    protected function getTemplateFileContent(FileTemplateInterface $fileTemplate) {
        if (is_readable($fileTemplate->getTemplateFilename())) {   //200mikrosec
            return \file_get_contents($this->template->getTemplateFilename());   //file_get_contents vrací FALSE při neúspěchu a E_WARNING, pokud neex soubor
        } else {
            throw new NoTemplateFileException('Nepodařilo se nalézt soubor "'.$fileTemplate->getTemplateFilename().'". Soubor neexistuje nebo jej nelze číst.');
        }
    }
}
