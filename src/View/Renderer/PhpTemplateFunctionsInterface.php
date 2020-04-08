<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\View\Renderer;

/**
 *
 * @author pes2704
 */
interface PhpTemplateFunctionsInterface {

    /**
     * Vloží šablonu, pokud jsou zadána data renderuje ji s použitím těchto dat. Pokud data nejsou zadána a je zadán třetí parametr - šablona pro případ prázdných dat,
     * použije tuto šablonu.
     *
     * @param type $templateFilename Plné jmé
     * @param type $data
     * @param type $emptyDataTemplateFilename
     */
    public function insert($templateFilename, $data=[], $emptyDataTemplateFilename='');

    /**
     * <p>Pokud jsou zadána data iteruje data, při každé iteraci vloží šablonu a renderuje ji s použitím jedné položky těchto dat.</p><p>
     * Data musí být iterovatelná příkazem foreach, musí tedy implementovat interface Traversable. Obvykle se jedná o pole. </p><p>
     * Jednotlivé položky dat mohou být asociativní pole s retězcovými (nečíselnými) indexy nebo skalár (typicky string). </p><p>
     * Pokud poležka pole dat je asociativní pole s retězcovými (nečíselnými) indexy, pak se opakovaně vkládaná šablona renderuje tak,
     * že jsou v ní k dispozici jednotlivé položky tohoto asociativnímo pole extrahované do proměnných se jmény odpovídajícími indexům pole.
     * Z toho také plyne zákaz číselnýcj indexů - z číselného indexu nevznikne platná php proměnná (např. $12) a takpvý prvek pole pak není
     * v šabloně nijak dostupný. </p><p>
     * Pokud položka pole dat je skalár, pak je v šabloně dostupná se jménem zadaným jako třetí parametr metody repeat ($variableName). </p><p>
     * Pokud data nejsou zadána a je zadán třetí parametr - šablona pro případ prázdných dat, použije tuto šablonu. </p><p>
     * Pokud data nejsou zadána a není zadán třetí parametr, může vracet prázdný řetězec nebo pevnou náhradní hodnotu. </p><p>
     *
     * @param type $templateFilename
     * @param type $data
     * @param type $emptyDataTemplateFilename
     */
    public function repeat($templateFilename, $data=[], $variableName, $emptyDataTemplateFilename='');

    // trait:
    public function filter($filters, $text);
    public function e($text);
    public function esc($text);
    public function mono($text);
    public function p($text);
    public function nl2br($text);
    public function attributes($array);
}
