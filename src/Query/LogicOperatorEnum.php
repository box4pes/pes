<?php
/**
 * Emuluje enum typ LogicOperator.
 * 
 * @author pes2704
 */
namespace Pes\Query;

use Pes\Type\Enum;

class LogicOperatorEnum extends Enum {    
    const AND_OPERATOR = 'AND';
    const OR_OPERATOR = 'OR';
}
