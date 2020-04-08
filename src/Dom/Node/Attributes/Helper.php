<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Dom\Node\Attributes;

/**
 * Description of Helper
 *
 * @author pes2704
 */
class Helper {
    
    /**
     * Převede atributy tagu zadané jako řetězec na asociativní pole atributů
     * 
     * @param type $input
     * @return type
     */
    public static function parse_attributes($input) {
      $dom = new DomDocument();
      $dom->loadHtml("<foo " . $input. "/>");
      $attributes = array();
      foreach ($dom->documentElement->attributes as $name => $attr) {
        $attributes[$name] = $node->value;
      }
      return $attributes;
    }
    
    /**
     * V zadaném HTML najde tag zadaný jménem a vrací jeho atributy
     * 
     * @param string $content HTML
     * @param string $tagName Jméno tagu, který se má v HTML vyhledat a persovat
     * @return type
     */
    public static function parseTag($content,$tagName) {
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $attr = array();
        foreach ($dom->getElementsByTagName($tagName) as $tag) {
            foreach ($tag->attributes as $attribName => $attribNodeVal)
            {
               $attr[$attribName]=$tag->getAttribute($attribName);
            }
        }
        return $attr;
    }
    
}
