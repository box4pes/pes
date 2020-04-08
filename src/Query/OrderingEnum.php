<?php
/**
 * Emuluje enum typ ColumnAccessEnum.
 * @uses Pes\Type\Enum

 * @author pes2704
 */
namespace Pes\Query;

use Pes\Type\Enum;

class OrderingEnum extends Enum {
    const ASCENDING = "ASC";
    const DESCENDING = 'DESC';    
}
