<?php
namespace Pes\Text;

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Description of Template
 *
 * @author pes2704
 */
class Template {

    /**
     * Použije $message jako šablonu a nahradí slova ve složených závorkách hodnotami pole $context s klíčem rovným nahrazovanému slovu.
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function interpolate($message, array $context = array()) {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            } else {
                $replace['{' . $key . '}'] = '';
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
