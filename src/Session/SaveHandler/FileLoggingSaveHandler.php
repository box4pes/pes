<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Session\SaveHandler;

use Psr\Log\LoggerInterface;

/**
 * Description of LoggingHandler
 *
 * @author pes2704
 */
class FileLoggingSaveHandler extends FileSaveHandler {

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    use LoggingReadWriteTrait;

    use LoggingHandlerTrait;
}
