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
     */
    protected function getTemplateFileContent(FileTemplateInterface $fileTemplate) {
        return $fileTemplate->getTemplateString();
    }
}
