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
interface InterpolateTemplateInterface extends FileTemplateInterface {

    public function getLeftBracket();

    public function getRightBracket();

}
