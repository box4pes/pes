<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\View\Renderer;

/**
 * Description of ArrayRendererInterface
 *
 * @author pes2704
 */
interface ArrayRendererInterface extends TemplateRendererInterface {
    public function render(array $data, $indexPrefix='');
}
