<?php
/**
 * Emuluje enum typ DbType.
 * 
 * @author pes2704
 */
namespace Pes\Database\Handler;

use Pes\Type\Enum;

class DbTypeEnum extends Enum {    
    const MySQL = 'mysql';
    const MSSQL = 'sqlsrv';
}
