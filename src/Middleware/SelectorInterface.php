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

/**
 *
 * @author pes2704
 */
interface SelectorInterface extends MiddlewareInterface {
    
    /**
     * Přijímá prefix a přiřazenou definici zásbníku middleware. Definice zásobníku middleware je:
     *  nebo pole Callable
     * <ul><li>Proměnná typu Psr\Http\Server\MiddlewareInterface - pak je volán tento jeden middleware</li>
     * <li>Pole, kde jednotlivé prvky pole jsou proměnné typu Psr\Http\Server\MiddlewareInterface
     * - pak je automaticky vytvořen objekt Dispatcher, který zajistí volání jednotlivých middleware ve stacku.</li>
     * <li>Closure, která vrací některý z výše uvedených typů, t.j. buď objekt typu Middleware nebo pole položek typu Middleware. Tato varianta
     * pracuje "lazy load" a jednotlivá middleware jsou instancována až v okamžiku jejich volání.</li>
     * <li></li>
     * </ul>
     * @param type $prefix
     * @param type $stack Definice zásobníku Middelware.
     * @param \Pes\Middleware\callable $resolver
     */
    public function addItem($prefix, $stack, callable $resolver=NULL);

}
