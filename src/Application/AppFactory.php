<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Application;

use Pes\Http\Environment;
use Pes\Http\Factory\ServerRequestFactory;
use Psr\Container\ContainerInterface;
use Pes\Container\Container;

use Pes\Session\SessionStatusHandlerInterface;
use Pes\Session\SessionStatusHandler;
use Pes\Session\SaveHandler\PhpSaveHandler;

/**
 * Description of AppFactory
 *
 * @author pes2704
 */
class AppFactory implements AppFactoryInterface {

    const SESSION_NAME_SERVICE='SESSION_NAME_SERVICE';
    const DEFAULT_SESSION_NAME = 'AppSession';
    const URI_INFO_ATTRIBUTE_NAME = 'uriInfo';

    protected $appContainer;

    public function __construct(ContainerInterface $appContainer=NULL) {
        $this->appContainer = $appContainer;
    }

    /**
     * Factory metoda, vytváří a vrací objekt App
     *
     * Objektu App nastaví vlastnosti request a pokud je v konstruktoru zadán kontejner, nastaví a nakonfiguruje ho jako kontejner aplikace.
     * Requestu nastaví jako atribut se jménem daným konstantou URI_INFO_ATTRIBUTE_NAME objekt UriInfo.
     * Vlastnosti request a kontejner jsou dostupné pomocí getterů objektu App.
     *
     *
     * Vlastnosti request a UriInfo jsou vytvořeny z superglobálních proměnných PHP $_SERVER, $_POST, $_GET, $_FILES.
     * Request je HTTP request došlý na server, tedy request, který spustil skript.
     * Pokud je v konstruktoru zadán kontejner, třída jej nakonfiguruje tak, že vždy obsahuje alespoň session handler. Session handler je nastaven tak,
     * že při použití (po zavolání služby kontejneru SessionStatusHandlerInterface::class) pracuje s daty session obsaženými v superglobální proměnné PHP $_SESSION.
     *
     * Kontejner aplikace je zde dále konfigurován takto:
     * <ul>
     * <li> Služby nastavené v kontejneru mají přednost před automaticky zde nastavovanými (dafault) službami.</li>
     * <li> Pro práci se session musí být ve výsledném kontejneru nakonfigurovány dvě služby - služba, která vrací jméno session a služby, která vrací session handler.
     *  <ul>
     *  <li>Jméno session musí vracet služba kontejneru se jménem daným konstantou třídy App::SESSION_NAME_SERVICE. Pokud služba se jménem App::SESSION_NAME_SERVICE nebyla nastavena v konfigurátoru kontejneru,
     * nastaví se zde tato služba tak, že vrací hodnotu danou konstantou třídy App::DEFAULT_SESSION_NAME.</li>
     * <li>Služba, která vrací session handler má vždy jméno Pes\Session\SessionStatusHandlerInterface::class, pokud není nakonfigurována služba se jménem Pes\Session\SessionStatusHandlerInterface::class, nastaví ji jako alias tak, že vrací Pes\Session\SessionStatusHandler.
     * Pokud není nakonfigurována služba Pes\Session\SessionStatusHandlerInterface::class, ale je nakonfigurována služba Pes\Session\SessionStatusHandler::class je nastaven jen tento alias.</li>
     * <li>Pokud není nakonfigurována v konfigurátoru služba Pes\Session\SessionStatusHandlerInterface::class ani služba Pes\Session\SessionStatusHandler::class, nastaví se zde služba Pes\Session\SessionStatusHandler::class tak,
     * že vrací Pes\Session\SessionStatusHandler s save handlerem typu Pes\Session\SaveHandler\PhpSaveHandler.</li>
     * </ul>
     *
     * @param ContainerInterface $this->appContainer Kontejner aplikace
     * @return AppInterface
     */
    public function createFromEnvironment(Environment $environment): AppInterface {
        $app = new App();

        // request
        $serverRequest = (new ServerRequestFactory())->createFromEnvironment($environment);
        $app->setServerRequest($serverRequest->withAttribute(self::URI_INFO_ATTRIBUTE_NAME, (new UriInfoFactory())->create($environment, $serverRequest)));

        // kontejner aplikace
        if ($this->appContainer) {
            // jméno session musí vracet služba kontejneru se jménem daným konstantou třídy App::SESSION_NAME_SERVICE, pokud není v konfigurátoru, definuji vlastní
            if ( !$this->appContainer->has(self::SESSION_NAME_SERVICE) ) {
                $this->appContainer->set(self::SESSION_NAME_SERVICE, self::DEFAULT_SESSION_NAME);
            }

            // session handler - pokud není v konfigurátoru, definuji vlastní
            // když není interface - definuji handler i interface jako alias, když hadler je a není interface, dodefinuji k handleru interface alias
            if ( !$this->appContainer->has(SessionStatusHandlerInterface::class)) {
                if ( !$this->appContainer->has(SessionStatusHandler::class)) {
                    $this->appContainer->set(SessionStatusHandler::class,
                        function(ContainerInterface $c) {
                                return new SessionStatusHandler($c->get(self::SESSION_NAME_SERVICE), new PhpSaveHandler() );
                            }
                        );
                }
                $this->appContainer->alias(SessionStatusHandlerInterface::class, SessionStatusHandler::class);
            }
        } else {
            $this->appContainer = new Container();
            $this->appContainer->set(self::SESSION_NAME_SERVICE, self::DEFAULT_SESSION_NAME);
                $this->appContainer->set(SessionStatusHandler::class,
                    function(ContainerInterface $c) {
                            return new SessionStatusHandler($c->get(self::SESSION_NAME_SERVICE), new PhpSaveHandler() );
                        }
                    );
            $this->appContainer->alias(SessionStatusHandlerInterface::class, SessionStatusHandler::class);
        }
        $app->setAppContainer($this->appContainer);
        return $app;
    }

}
