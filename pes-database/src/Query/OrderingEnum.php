<?php
/**
 * Emuluje enum typ ColumnAccessEnum.
 * @uses Pes\Core\Type\Enum

 * @author pes2704
 */
namespace Pes\Query;

use Pes\Core\Type\Enum;

class OrderingEnum extends Enum {
    const ASCENDING = "ASC";
    const DESCENDING = 'DESC';    
}
