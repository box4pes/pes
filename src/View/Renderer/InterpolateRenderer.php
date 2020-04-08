<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

use Pes\View\Template\TemplateInterface;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

/**
 * Description of ScriptView
 *
 * @author pes2704
 */
class InterpolateRenderer extends FileTemplateRendererAbstract implements InterpolateRendererInterface {

    private $template;


    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== InterpolateRenderer::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($this->template));
        }
        $this->template = $template;
    }

    /**
     * Použije text souboru jako šablonu a nahradí slova v závorkách hodnotami pole dat s klíčem rovným nahrazovanému slovu.
     *
     * Pokud některá hodnota není definována nebo ji nelze převést na string - nahradí slovo v závorkách prázdným řetězcem a Hlásí E_USER_WARNING.
     *
     * @param type $data Array nebo \Traversable
     * @return string
     * @throws NoTemplateFileException
     */
    public function render(iterable $data=NULL) {
        $text = $this->getTemplateFileContent($this->template);
        if ($text) {
            if (isset($data)) {
            $replace = [];
            $leftBracket = $this->template->getLeftBracket();
            $righrBracket = $this->template->getRightBracket();
            // sestav pole náhrad
            foreach ($data as $key => $val) {
                // ověř, že hodnota může být převedena na string
                if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                    $replace[$leftBracket . $key . $righrBracket] = $val;
                } else {
                    $replace[$leftBracket . $key . $righrBracket] = '';
                    user_error("Hodnotou s klíčem $key nelze interpolovat, hodnota není definována nebo hodnotu nelze převést na string.", E_USER_WARNING);
                }
            }
                // interpoluj náhrady do textu
                return strtr($text, $replace);
            } else {
                return $text;
            }
        } else {
            return '';
        }
    }
}
