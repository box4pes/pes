<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler\DsnProvider;

use Psr\Log\LoggerInterface;

/**
 * Description of DsnProviderAbstract
 *
 * @author pes2704
 */
abstract class DsnProviderAbstract implements DsnProviderInterface {

    /** @var LoggerInterface */
    protected $logger;

    /**
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }
}
