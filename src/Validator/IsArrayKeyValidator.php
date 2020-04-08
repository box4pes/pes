<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Validator;

use Pes\Validator\Exception\NotArrayKeyException;

/**
 * IsStringValidator ověřuje jestli parametr je string nebo integer (přetypovatelný na string.
 *
 * @author pes2704
 */
class IsArrayKeyValidator implements ValidatorInterface {
    public function validate($param):void {
        if ( !is_string($param) AND !is_integer($param)) {
            throw new NotArrayKeyException("Value is not valid array key.");
        }
    }
}
