<?php

/*
 * Copyright (C) 2019 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Statement\Exception;

use Throwable;

/**
 * StatementException extends PDOException
 *
 * @author pes2704
 */
class StatementException extends \PDOException {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = NULL) {
        if (isset($previous)) {
            parent::__construct($message.PHP_EOL.$previous->getTraceAsString(), $code, $previous);
        } else {
            parent::__construct($message, $code, $previous);
        }
    }
}
