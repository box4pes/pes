<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Statement;

use Psr\Log\LoggerAwareInterface;

/**
 *
 * @author pes2704
 */
interface StatementInterface extends PDOStatementInterface, LoggerAwareInterface, \Traversable {

    /**
     * Vrací informaci umožňující rozlišit konkrétní instanci objektu, obvykle pro účely debugování a logování.
     * Implememntace NESMÍ vracet citlivé informace o databázovém připojení.
     */
    public function getInstanceInfo();
}
