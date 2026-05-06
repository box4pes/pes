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

/**
 * Description of AppFactory
 *
 * <b>Jak session znovu zapojit</b>
 *
 * Kontejner musí být {@see \Pes\Container\ContainerSettingsAwareInterface} (např. {@see \Pes\Container\Container}):
 *
 * <code>
 * use Pes\Application\AppFactory;
 * use Pes\Container\Container;
 * use Pes\Http\Factory\EnvironmentFactory;
 * use Pes\Session\Container\SessionServicesConfigurator;
 *
 * $container = new Container();
 * SessionServicesConfigurator::registerDefaults($container);
 *
 * $app = (new AppFactory($container))->createFromEnvironment((new EnvironmentFactory())->createFromGlobals());
 * </code>
 *
 * Pokud nejdřív zavoláš <code>(new AppFactory())->createFromEnvironment(...)</code>, musíš před prvním použitím
 * session služeb zavolat {@see \Pes\Session\Container\SessionServicesConfigurator::registerDefaults()} na instanci kontejneru,
 * která implementuje {@see \Pes\Container\ContainerSettingsAwareInterface} (typ vrácený z {@see \Pes\Application\AppInterface::getAppContainer()}
 * je jen {@see \Psr\Container\ContainerInterface}, takže je potřeba znát konkrétní typ nebo použít výše uvedený postup s vlastním {@see \Pes\Container\Container}).
 *
 * @author pes2704
 */
class AppFactory implements AppFactoryInterface {

    const URI_INFO_ATTRIBUTE_NAME = 'uriInfo';

    protected $appContainer;

    public function __construct(?ContainerInterface $appContainer = null) {
        $this->appContainer = $appContainer;
    }

    /**
     * Factory metoda, vytváří a vrací objekt App
     *
     * Objektu App nastaví vlastnosti request a pokud je v konstruktoru zadán kontejner, použije ho jako kontejner aplikace.
     * Není-li kontejner zadán, vytvoří prázdný Pes\Container\Container.
     *
     * Pro výchozí registraci služeb PHP session v kontejneru zavolejte
     * {@see \Pes\Session\Container\SessionServicesConfigurator::registerDefaults()} (balíček pes/pes-session).
     *
     * Vlastnosti request a UriInfo lze vytvořit z superglobálních proměnných PHP $_SERVER, $_POST, $_GET, $_FILES.
     *
     * @return AppInterface
     */
    public function createFromEnvironment(Environment $environment): AppInterface {
        $app = new App();

        // request
        $serverRequest = (new ServerRequestFactory())->createFromEnvironment($environment);
        //$app->setServerRequest($serverRequest->withAttribute(self::URI_INFO_ATTRIBUTE_NAME, (new UriInfoFactory())->create($environment, $serverRequest)));

        if (!$this->appContainer) {
            $this->appContainer = new Container();
        }
        $app->setAppContainer($this->appContainer);
        return $app;
    }

}
