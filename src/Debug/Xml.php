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
 * Description of Xml
 *
 * @author pes2704
 */
class Xml {
//header('Content-Type: text/xml; charset=UTF-8');
//echo print_r_xml($some_var);

    public static function print_r_xml($arr,$first=true) {
      $output = "";
      if ($first) $output .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<data>\n";
      foreach($arr as $key => $val) {
        if (is_numeric($key)) $key = "arr_".$key; // <0 is not allowed
        switch (gettype($val)) {
          case "array":
            $output .= "<".htmlspecialchars($key)." type='array' size='".count($val)."'>".
              print_r_xml($val,false)."</".htmlspecialchars($key).">\n"; break;
          case "boolean":
            $output .= "<".htmlspecialchars($key)." type='bool'>".($val?"true":"false").
              "</".htmlspecialchars($key).">\n"; break;
          case "integer":
            $output .= "<".htmlspecialchars($key)." type='integer'>".
              htmlspecialchars($val)."</".htmlspecialchars($key).">\n"; break;
          case "double":
            $output .= "<".htmlspecialchars($key)." type='double'>".
              htmlspecialchars($val)."</".htmlspecialchars($key).">\n"; break;
          case "string":
            $output .= "<".htmlspecialchars($key)." type='string' size='".strlen($val)."'>".
              htmlspecialchars($val)."</".htmlspecialchars($key).">\n"; break;
          default:
            $output .= "<".htmlspecialchars($key)." type='unknown'>".gettype($val).
              "</".htmlspecialchars($key).">\n"; break;
        }
      }
      if ($first) $output .= "</data>\n";
      return $output;
    }

}
