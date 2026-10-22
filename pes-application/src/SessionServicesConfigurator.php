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

use Pes\Container\ContainerSettingsAwareInterface;
use Pes\Session\SaveHandler\PhpSaveHandler;
use Pes\Session\SessionStatusHandler;
use Pes\Session\SessionStatusHandlerInterface;
use Psr\Container\ContainerInterface;

/**
 * Zaregistruje výchozí služby pro práci se session v kontejneru (jméno session, SessionStatusHandler).
 *
 * Volitelně po vytvoření aplikace: SessionServicesConfigurator::registerDefaults($appContainer);
 */
class SessionServicesConfigurator {

    /**
     * Služba kontejneru, která vrací řetězec s názvem session (cookie).
     */
    public const SESSION_NAME_SERVICE = 'SESSION_NAME_SERVICE';

    /**
     * Výchozí název session, pokud není služba SESSION_NAME_SERVICE předefinována.
     */
    public const DEFAULT_SESSION_NAME = 'AppSession';

    public static function registerDefaults(ContainerSettingsAwareInterface $container): void {
        if (!$container->has(self::SESSION_NAME_SERVICE)) {
            $container->set(self::SESSION_NAME_SERVICE, self::DEFAULT_SESSION_NAME);
        }

        if (!$container->has(SessionStatusHandlerInterface::class)) {
            if (!$container->has(SessionStatusHandler::class)) {
                $container->set(SessionStatusHandler::class,
                    function (ContainerInterface $c) {
                        return new SessionStatusHandler($c->get(self::SESSION_NAME_SERVICE), new PhpSaveHandler());
                    }
                );
            }
            $container->alias(SessionStatusHandlerInterface::class, SessionStatusHandler::class);
        }
    }
}
