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

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Description of ContainerMiddlewareAbstract
 *
 * @author pes2704
 */
abstract class AppMiddlewareAbstract implements AppMiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    protected $app;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function getApp(): AppInterface {
        return $this->app;
    }

    public function setApp(AppInterface $app): AppMiddlewareInterface {
        $this->app = $app;
        return $this;
    }

    public function getLogger(): LoggerInterface {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): AppMiddlewareInterface {
        $this->logger = $logger;
        return $this;
    }
}
