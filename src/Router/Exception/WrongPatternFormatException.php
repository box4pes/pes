<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router\Exception;

use Pes\Validator\Exception\ValidatorExceptionInterface;

/**
 * Description of WrongPatternFormat
 *
 * @author pes2704
 */
class WrongPatternFormatException extends \UnexpectedValueException implements RouterExceptionInterface, ValidatorExceptionInterface {
    //put your code here
}
