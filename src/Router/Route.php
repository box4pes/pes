<?php

namespace Pes\Router;

use Pes\Router\Resource\ResourceInterface;

/**
 * Description of Route
 *
 * @author pes2704
 */
class Route implements RouteInterface {

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $patternPreg;

    /**
     * @var string
     */
    private $action;

    public function getResource(): ResourceInterface {
        return $this->resource;
    }

    /**
     *
     * @return string Vrací regulární výraz vytvořený z parametru urlPattern
     */
    public function getPatternPreg() {
        return $this->patternPreg;
    }

    /**
     * @return callable Vrací spustitelnou akci routy.
     */
    public function getAction() {
        return $this->action;
    }

    /**
     *
     * @param ResourceInterface $resource
     * @return \Pes\Router\RouteInterface
     */
    public function setResource(ResourceInterface $resource): RouteInterface {
        $this->resource = $resource;
        // konvertuje route url na regulární výraz - obalí pattern routy znaky začátku a konce regulárního výrazu
        // a nahradí části začínající : výrazem ([a-zA-Z0-9\-\_]+)
        // Příklad: url "/node/:id/add/" kovertuje na regulární výraz "@^/node/([a-zA-Z0-9\-\_]+)/add/$@Du"
        // D a u jsou pattern modifiers- D způsobí rozpoznání znaku $ jen ka konci vzoru a u způsobí vyhodnocování řetězsů jako utf8
        // když není nastaveno u -> neumí utf8 jen ascii a tedy neumí písmenka s diakritikou
        
        // původní verze ascii: $this->patternPreg = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/u', '([a-zA-Z0-9\-\_]+)', preg_quote($this->resource->getUrlPattern())) . "$@Du";
        
        $this->patternPreg = "@^" . preg_replace('/\\\:[[:alpha:][:digit:]\-\_]+/u', '([[:alpha:][:digit:]\-\_]+)', preg_quote($this->resource->getUrlPattern())) . "$@Du";   // nastaveno pro utf8
       return $this;
    }

    /**
     *
     * @param callable $action
     * @return \Pes\Router\RouteInterface
     */
    public function setAction(callable $action): RouteInterface {
        $this->action = $action;
        return $this;
    }


}
