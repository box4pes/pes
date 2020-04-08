<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Handler;

use Psr\Log\LoggerAwareInterface;
/**
 *
 * @author pes2704
 */
interface HandlerInterface extends PDOInterface, LoggerAwareInterface {
    /**
     * Metoda getInstanceInfo
     *
     * Vrací unikátní identifikátor handleru.
     *
     */
    public function getInstanceInfo();

    /**
     * Metoda getSchemaName
     *
     * Vrací jméno databáze, ke které je handler aktuálně připojen. Znalost skutečného jména databáze umožňuje provádění SQL dotazů například na tabulky information_schema.
     *
     * @return string
     */
    public function getSchemaName(): string;
}
