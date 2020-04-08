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
            throw new WrongPatternFormatException("Chybný formát pattern. Pattern routy nesmí být prázdný řetězec.");
        }
        if ($urlPattern[0] != '/') {
            throw new WrongPatternFormatException("Chybný formát pattern. Pattern routy musí začínat znakem '/'. Zadán pattern: $urlPattern");
        }
//        if ($urlPattern[-1] != '/') {
//            throw new WrongPatternFormatException("Chybný formát pattern. Pattern routy musí končit znakem '/'. Zadán pattern: $urlPattern");
//        }
        if (($urlPattern[1] ?? '') == ':') {
            throw new WrongPatternFormatException("Chybný formát pattern. Pattern routy nesmí na první pozici zleva obsahovat parametr. Zadán pattern: $urlPattern");
        }
    }
}
