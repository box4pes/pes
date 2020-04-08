<?php

namespace Pes\Router;

/**
 * Description of Route
 *
 * @author pes2704
 */
class Route implements RouteInterface {

    /**
     * @var UrlPatternValidator
     */
    private $urlPatternValidator;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $urlPattern;

    /**
     * @var string
     */
    private $patternPreg;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $name;

    public function __construct(UrlPatternValidator $urlPatternValidator) {
        $this->urlPatternValidator = $urlPatternValidator;
    }

    public function getMethod() {
        return $this->method;
    }

    /**
     *
     * @return string Vrací zadaný urlPattern
     */
    public function getUrlPattern() {
        return $this->urlPattern;
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
     * @return callable
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * Přijímá hodnoty výčtového typu MethodEnum. V případě neexistující hodnoty vyhodí objekt MethodEnum svoji výjimku.
     *
     * @param string $method Existující hodnota výčtového typu MethodEnum.
     * @return \Pes\Router\RouteInterface
     */
    public function setMethod($method): RouteInterface {
        $this->method = (new MethodEnum())($method);
        return $this;
    }

    /**
     * Nastaví pattern routy. Kontroluje přípustný formát pattern a v případě chybného formátu vyhodí výjimnku.
     * Pattern routy začíná i končí znakem '/' a může obsahovat segmenty oddělené znakem '/'. Pattern, který nemá segmenty je '/'.
     * Jednotlivé segmenty jsou dvojího druhu:
     *
     * @param string $urlPattern
     * @return \Pes\Router\RouteInterface
     * @throws \UnexpectedValueException Chybný formát pattern...
     */
    public function setUrlPattern($urlPattern): RouteInterface {    // 50 microsec
        $this->urlPatternValidator->validate($urlPattern);
        $this->urlPattern = $urlPattern;
        // konvertuje route url na regulární výraz - obalí pattern routy znaky začátku a konce regulárního výrazu
        // a nahradí části začínající : výrazem ([a-zA-Z0-9\-\_]+)
        // Příklad: url "/node/:id/add/" kovertuje na regulární výraz "@^/node/([a-zA-Z0-9\-\_]+)/add/$@D"
        // když není nastaveno /u -> neumí utf8 jen ascii a tedy neumí písmenka s diakritikou
        $this->patternPreg = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/u', '([a-zA-Z0-9\-\_]+)', preg_quote($this->urlPattern)) . "$@D";
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
