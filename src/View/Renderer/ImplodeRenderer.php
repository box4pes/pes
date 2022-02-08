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
use Pes\View\Template\ImplodeTemplateInterface;
use Pes\View\Renderer\Exception\UnsupportedTemplateException;

/**
 * ImplodeRenderer pouze zřetězí data s použitím separátoru zadaného v konstruktoru, použije php funkci implode().
 *
 * @author pes2704
 */
class ImplodeRenderer implements TemplateRendererInterface {

    /**
     * @var TemplateInterface
     */
    private $template;

    public function setTemplate(TemplateInterface $template) {
        if ($template->getDefaultRendererService() !== ImplodeRenderer::class) {
            throw new UnsupportedTemplateException("Renderer ". get_called_class()." nepodporuje renderování template typu ". get_class($template));
        }
        $this->template = $template;
    }

    /**
     * Zřetězí data jako string složený z hodnot oddělených (slepených) separátorem.
     *
     * @param ImplodeTemplateInterface $template
     * @param iterable $data Array nebo objekt Traversable.
     * @return string
     * @throws \UnexpectedValueException
     */
    public function render(iterable $data=NULL) {
        if (isset($this->template)) {
            $separator = $this->template->getSeparator();
        } else {
            $separator = ImplodeTemplateInterface::SEPARATOR;
        }
        if ($data) {
            if (is_array($data) OR $data  instanceof \Traversable) {
                $str = $this->implodeRecursive($separator, $data);
            } else {
                throw new \UnexpectedValueException("Data musí být array nebo Traversable.");
            }
        }
        return $str ?? '';
    }

    private function implodeRecursive($separator, $data) {
        $arr = [];
        foreach($data as $value) {
            if(is_array($value) OR $value  instanceof \Traversable) {
                $arr[] = $this->implodeRecursive($separator, $value);
            } else {
                $arr[] = (string) $value;
            }
        }
        return implode( $separator, $arr );
    }

}
