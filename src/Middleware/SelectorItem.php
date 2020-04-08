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

use Pes\Application\AppInterface;
use Pes\Middleware\AppMiddlewareInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Description of Prefix
 *
 * @author pes2704
 */
class SelectorItem implements SelectorItemInterface {

    private $prefix;
    private $stack;
    private $resolver;

    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Vrací middleware vytvořené ze stacku zadaného metodou setStack().
     * Pokud stack je:
     * <ul>
     * <li>Pole, vytvoří z tohoto pole objekt Dispatcher (ten je typu MiddlewareInterface) a ten vrací.</li>
     * <li>Objekt MiddlewareInterface, vrací tento objekt.</li>
     * <li>callable - provede volání callable, očekává, že callable vrací pole nebo objekt MiddlewareInterface, pak s návratovou hodnotou z callable
     * zachází jako s přímo zadaným parametrem. Tedy:
     * <ul>
     *  <li>Pokud callable vrátí pole, vytvoří z tohoto pole objekt Dispatcher (ten je typu MiddlewareInterface) a ten vrací.</li>
     *  <li>Pokud callable vrací objekt MiddlewareInterface, vrací tento objekt.</li>
     *  <li>V ostatních případech vyhodí výjimku \UnexpectedValueException("Nepřípustný typ položky selektoru...)</li>
     *  </ul>
     * <li>V ostatních případech vyhodí výjimku \UnexpectedValueException("Nepřípustný typ položky selektoru...)</li>
     * </ul>
     * Pokud je stack typu callable, pak je interně tato callable proměnná volána.
     * Pokud byl konstruktoru objektu Selector zadán nepovinný parametr metody AppInterface $app, je tento parametr předán jako parametr při volání callable.
     * Příklad:
     * <pre><code>
     * $selector = new Selector($app); //$app je typu Pes\Application
     * $selector->addItem('aaa', function(AppInterface $app) { vykonatelný kód middleware })
     * $app->run($selector);  // ve vykonatelném kódu middleware je dostupná proměnná $app
     * </code></pre>
     *
     * @param AppInterface $app
     * @return MiddlewareInterface
     * @throws \UnexpectedValueException
     */
    public function getMiddleware(AppInterface $app=NULL): MiddlewareInterface {
        if (is_callable($this->stack)) {
            $stack = $this->stack;
            $this->stack = $stack($app);
        }
        if (is_array($this->stack)) {
            return new Dispatcher($this->stack, $this->resolver, $app);
        } elseif ($this->stack instanceof MiddlewareInterface) {
            if ($this->stack instanceof AppMiddlewareInterface) {
                $this->stack->setApp($app);
            }
            return $this->stack;
        } else {
            $type = gettype($this->stack);
            throw new \UnexpectedValueException("Nepřípustný typ položky selektoru pro prefix {$this->prefix}. Položka selektoru musí být array nebo MiddlewareInterface, zadaný typ nebo typ vrácený parametrem callable je $type.");
        }
    }

    public function setPrefix($prefix): SelectorItem {
        $this->prefix = trim($prefix);
        return $this;
    }
    /**
     * Nastaví definici zásbníku middleware. Definice zásobníku middleware je:
     * <ul><li>Proměnná typu Psr\Http\Server\MiddlewareInterface - pak je volán tento jeden middleware</li>
     * <li>Pole, kde jednotlivé prvky pole jsou proměnné typu Psr\Http\Server\MiddlewareInterface - pak je toto pole použitojako definice stacku,
     * je automaticky vytvořen objekt Dispatcher, který zajistí volání jednotlivých middleware ve stacku.</li>
     * <li>Closure, která vrací některý z výše uvedených typů, t.j. buď objekt typu Middleware nebo pole položek typu Middleware. Tato varianta pak
     * pracuje "lazy load" a jednotlivá middleware jsou instancována až v okamžiku jejich volání.</li>
     * </ul>
     * @param type $stack
     * @param \Pes\Middleware\callable $resolver
     * @return \Pes\Middleware\SelectorItem
     */
    public function setStack($stack, callable $resolver = NULL): SelectorItem {
        $this->stack = $stack;
        $this->resolver = $resolver;
        return $this;
    }

}
