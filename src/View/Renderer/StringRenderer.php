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
 * ImplodeRenderer pouze zřetězí data s použitím separátoru zadaného v konstruktoru, použije php funkci implode().
 * Má svůj interface. Nemplementuje RendereInterface!!
 *
 * @author pes2704
 */
class StringRenderer implements StringRendererInterface {

    /**
     * Převede data na string.
     *
     * @param type $data
     * @return type
     */
    public function render($data=NULL) {
        if ($data) {
            $str = (string) $data;
        }
        return $str ?? '';
    }
}
