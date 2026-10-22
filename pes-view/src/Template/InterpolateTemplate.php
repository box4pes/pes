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

use Pes\View\Renderer\InterpolateRenderer as DefaultRenderer;

/**
 * PhpTemplate.
 *
 * @author pes2704
 */
class InterpolateTemplate extends FileTemplateAbstract implements InterpolateTemplateInterface {


    const LEFT_BRACKET = "{{";
    const RIGHT_BRACKET = "}}";

    private $leftBracket;
    private $rightBracket;

    public function __construct($templateFileName, $leftBracket = self::LEFT_BRACKET, $rightBracket = self::RIGHT_BRACKET) {
        parent::__construct($templateFileName);
        $this->leftBracket = $leftBracket;
        $this->rightBracket = $rightBracket;
    }

    /**
     * Vrací jméno třídy InterpolateRenderer.
     * @return string
     */
    public function getDefaultRendererService() {
        return DefaultRenderer::class;
    }

    public function getLeftBracket() {
        return $this->leftBracket;
    }

    public function getRightBracket() {
        return $this->rightBracket;
    }
}
