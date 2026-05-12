<?php

namespace Pes\Database\Handler\Mini;

/**
 *
 * @author pes2704
 */
interface DsnInterface {
    public function getDsnString();
    public function getDbName();
    public function getDbHost();
}
