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

use Pes\Validator\Exception\NotSerialisableException;

/**
 * IsSerializableValidator ověčuje zda parametr je jistě serializovatelný.
 * Za serializovatelné jsou považovány všechny PHP typy mimo resource a callable (Closure)
 * a objekty pouze v případě, že implementují rozhraní Serializable.
 *
 * @author pes2704
 */
class IsSerializableValidator implements ValidatorInterface {

    public function validate($param): void {
        if (
                is_callable($param)
                OR is_resource($param)
                OR !(is_object($param))
                OR (is_object($param) AND $param instanceof \Serializable)
            )  {
                throw new NotSerialisableException();
            }
    }
}
