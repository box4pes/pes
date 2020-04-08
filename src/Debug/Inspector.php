<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Debug;

/**
 * Description of Inspector
 * https://stackoverflow.com/questions/255312/how-to-get-a-variable-name-as-a-string-in-php
 *
 * @author pes2704
 */
class Inspector {

    public static function inspect($variable) {
        $backTrace = debug_backtrace();
        $varName = self::getVarname($backTrace);
        return "<p>".$varName."</p>"."<pre>". print_r($variable, TRUE)."</pre>";
    }
    
    private static function getVarname($backTrace) {
        $src = file($backTrace[0]["file"]);
        $line = $src[ $backTrace[0]['line'] - 1 ];

        // let's match the function call and the last closing bracket
        preg_match( "#inspect\((.+)\)#", $line, $match );

        /* let's count brackets to see how many of them actually belongs
           to the var name */
        $max = strlen($match[1]);
        $varname = "";
        $c = 0;
        for($i = 0; $i < $max; $i++){
            if(     $match[1]{$i} == "(" ) $c++;
            elseif( $match[1]{$i} == ")" ) $c--;
            if($c < 0) break;
            $varname .=  $match[1]{$i};
        }
        return $varname;

    }

}
