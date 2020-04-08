<?php

namespace Pes\Database\Handler\Mini;

/**
 * Description of Dsn
 *
 * @author pes2704
 */
class Dsn implements DsnInterface {

    private $dbHost;
    private $dbName;

    public function __construct($dbHost, $dbName) {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
    }
    public function getDsnString() {
        return 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName ;
    }

    public function getDbHost() {
        return $this->dbHost;
    }

    public function getDbName() {
        return $this->dbName;
    }


}
