<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Template;

use Pes\View\Renderer\ImplodeRendererInterface;

use Pes\View\Renderer\ImplodeRenderer as DefaultRenderer;

/**
 *
 * @author pes2704
 */
class ImplodeTemplate implements ImplodeTemplateInterface {


    const SEPARATOR = PHP_EOL;

    private $separator;

    public function __construct($separator = self::SEPARATOR) {
        $this->separator = $separator;
    }

    public function getSeparator() {
        return $this->separator;
    }

    public function getDefaultRendererService() {
        return DefaultRenderer::class;
    }
}
