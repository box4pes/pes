<?php

namespace Pes\Database\Handler;

/**
 *
 * @author pes2704
 */
interface ConnectionInfoInterface {

    /**
     * @return DbTypeEnum Typ databáze jako výčtový typ
     */
    public function getDbType();

    /**
     * @return
     */
    public function getDbHost();

    /**
     * @return string Označení kódování pro připojení (Connection Character Set)
     */
    public function getCharset();

    /**
     * @return string Označení řazení pro připojení (Connection Collation)
     */
    public function getCollation();

    /**
     * @return string Skutečné aktuální jméno databáze
     */
    public function getDbName();

    /**
     * @return integer Číslo portu
     */
    public function getDbPort();

}
