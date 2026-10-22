<?php

namespace Pes\Model\Exception;

use LogicException;

/**
 * Session byla ukončena přes StatusDao::finish() (nebo je PHP session neaktivní).
 * Další čtení/zápis status dat přes Dao / StatusRepository není povoleno.
 */
class SessionFinishedException extends LogicException {
}
