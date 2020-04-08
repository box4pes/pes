<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Pes\Application\AppInterface;

/**
 *
 * @author pes2704
 */
interface SelectorItemInterface {
    public function getPrefix();
    public function getMiddleware(AppInterface $app=NULL): MiddlewareInterface;
    public function setPrefix($prefix): SelectorItem;
    public function setStack($stack, callable $resolver = NULL): SelectorItem;
}
