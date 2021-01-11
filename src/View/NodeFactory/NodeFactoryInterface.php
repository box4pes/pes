<?php

namespace Pes\View\NodeFactory;

use Pes\Dom\Node\NodeInterface;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface NodeFactoryInterface {
    public function create(): NodeInterface;
}
