<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Router;

use Pes\Validator\ValidatorInterface;
use Pes\Router\Exception\WrongPatternFormatException;

/**
 * Description of UrlPatternValidator
 *
 * @author pes2704
 */
class UrlPatternValidator implements ValidatorInterface {
    public function validate($urlPattern): void {
        if ($urlPattern == '') {
            throw new WrongPatternFormatException("Wrong pattern. Pattern must not be empty.");
        }
        if ($urlPattern[0] != '/') {
            throw new WrongPatternFormatException("Wrong pattern '$urlPattern'. First character of pattern must be '/'.");
        }
        if (($urlPattern[1] ?? '') == ':') {
            throw new WrongPatternFormatException("Wrong pattern '$urlPattern'. First segment of pattern must not contain parameter.");
        }
    }
}
