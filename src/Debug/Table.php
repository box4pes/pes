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
 * Description of Table
 *
 * @author pes2704
 */
class Table {

    const LEVEL_INFO = 'INFO';
    const LEVEL_FULL = 'FULL';

    private static $level=self::LEVEL_INFO;

    public static function SetLevel($level=self::LEVEL_INFO) {
        self::$level = $level;
    }

    public static function Table($iterable) {
        if (is_iterable($iterable)) {
        // start table
        $html = '<table class="debugTable">';

        // data rows
        $firstRow = TRUE;
        $th = '';
        $td = '';
        foreach( $iterable as $key=>$value){
    //        if ($firstRow) {
                $th .= '<th>';
                    $th .= htmlspecialchars($key);
                $th .= '</th>';
    //        }
            $td .= '<td>';
            if ($value instanceof \Traversable) {
                $td .= self::Table($value);
            } else {
                $td .= self::Value($value);
            }
            $td .= '</td>';
        }

            $html .= '<thead>';
        $html .= '<tbody>';

    //        if ($firstRow) {
                $html .= '<tr>';
                $html .= $th;
                $html .= '</tr>';
    //        }
            $html .= '</thead>';
            $html .= '<tr>';
                $html .= $td;
            $html .= '</tr>';

            $firstRow = FALSE;
        $html .= '</tbody>';

        // finish table and return it

        $html .= '</table>';
        } else {
            $html = self::Value($iterable);
        }
        return $html;
    }

    private static function Value($value) {
        $vartype = gettype($value);
        switch (self::$level) {

            case self::LEVEL_INFO:
                return self::renderValueAsInfo($value);
            case self::LEVEL_FULL:
                return self::renderValueFull($value);
        }
    }

    private static function renderValueAsInfo($var) {
        $vartype = gettype($var);
        switch ($vartype) {
            case "boolean":
                $rendered = $vartype." ".($var ? "TRUE" : "FALSE");
                break;
            case "integer":
            case "double":    // (for historical reasons "double" is returned in case of a float, and not simply
            case "float":
                $rendered = $vartype." ".$var;
                break;
            case "string":
                $rendered = $vartype." ". strlen($var)." bytes";
                break;
            case "array":
                $rendered = $vartype." ".count($var)." elements";
                break;
            case "object":
            case "resource":
                $rendered = $vartype." ". get_class($var);
                break;
            case "NULL":
            case "unknown type":
                $rendered = $vartype;
                break;

        }

        return $rendered;
    }

    private static function renderValueFull($var) {
        $vartype = gettype($var);
        switch ($vartype) {
            case "boolean":
                $rendered = $vartype." ".($var ? "TRUE" : "FALSE");
                break;
            case "integer":
            case "double":    // (for historical reasons "double" is returned in case of a float, and not simply
            case "float":
                $rendered = $vartype." ".$var;
                break;
            case "string":
                $rendered = $vartype." ". strlen($var)." bytes" . (strlen($var) ? ": \"".$var."\"" : "");
                break;
            case "array":
                $rendered = $vartype." ".count($var)." elements";
                break;
            case "object":
            case "resource":
                $rendered = $vartype." ". get_class($var);
                break;
            case "NULL":
            case "unknown type":
                $rendered = $vartype;
                break;

        }

        return $rendered;
    }


    public function getStyle($param) {
return
"<style>
table.debugTable {
    font-family: arial, sans-serif;
    font-size: .9em;
    border-collapse: collapse;
    width: 100%;
}

table.debugTable td, th {
    border: 1px solid #cccccc;
    text-align: left;
    padding: 3px;
}

table.debugTable th {
    background-color: #4CAF50;
    color: white;
}

table.debugTable tr {
    padding: 3px;
}

table.debugTable tr:nth-child(even) {
    background-color: #dddddd;
}

p.error {
    background-color: lightred;
}
p.domain {
    background-color: lightyellow;
}
p.user {
    background-color: lightblue;
}
</style>";
    }
}
