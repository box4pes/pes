<?php
/**
 * Emuluje enum typ ColumnAccessEnum.
 * @uses Pes\Type\Enum
 * 
 * @author pes2704
 */
namespace Pes\Query;

use Pes\Type\Enum;

class ColumnAccessEnum extends Enum {
    const DEFAULT_ACCESS = "default_access";
    const WRITING_PROHIBITED = 'writing_prohibited';
    const UPDATE_PROHIBITED = 'update_prohibited';    
    const ALWAYS_WRITEABLE = 'always_writeable';
}
