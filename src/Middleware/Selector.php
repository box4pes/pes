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

use Pes\Middleware\SelectorItem;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Application\AppFactory;
use Pes\Application\UriInfoInterface;

/**
 * Description of Switch
 *
 * @author pes2704
 */
class Selector extends AppMiddlewareAbstract implements SelectorInterface, AppMiddlewareInterface {  // AppMiddlewareINterface

    /**
     * @var SelectorItem array of
     */
    private $items = array();

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
    public function addItem($prefix, $stack, callable $resolver=NULL): SelectorInterface{
        $this->items[] = (new SelectorItem())->setPrefix($prefix)->setStack($stack, $resolver);
        return $this;
    }

    /**
     *
     * @param array $items
     * @return \Pes\Middleware\SelectorInterface
     */
    public function addItemsArray(array $items): SelectorInterface {
        foreach ($this->items as $prefix=>$stack) {
            $this->addItem($prefix, $stack);
        }
        return $this;
    }

    /**
     * Vybere selector item podle prefixu. Z vybraného item vyzvedne middleware. Middleware je definovaný při přidávání itemu do selektoru.
     * Při vyzvednutí middleware z itemu je v případě, že middleware je definován pomocí callable (např. anonymní funkce) tato callable je zavolána.
     * Pokud byl selektoru zadán jako parametr konstruktoru kontejner, je tento kontejner předán jako parametr při volání tétoo callable. Tak je možné předat kontejner (globální kontejner aplikace) do
     * jednotlivých middleware.
     * Vyzvednutý middleware pak spouští zavoláním metody process tohoto middleware.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
//        $path = $request->getUri()->getPath();
        /** @var UriInfoInterface $uriInfo */
        $uriInfo = $request->getAttribute(AppFactory::URI_INFO_ATTRIBUTE_NAME);
        $restUri = $uriInfo->getRestUri();
        foreach($this->items as  $item) {
            if(strpos($restUri, $item->getPrefix())===0) {
                $middleware = $item->getMiddleware($this->app);
                if($this->logger) {
                    $this->logger->debug("Selector: Pro REST uri $restUri a prefix {$item->getPrefix()} vybrán middleware ".get_class($middleware).".");
                }
                return $middleware->process($request, $handler);
            }
        }
        return $handler->handle($request);
    }
}
