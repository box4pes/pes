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
     * Vloží šablonu, pokud jsou zadána data renderuje ji s použitím těchto dat. Pokud data jsou prázdná a je zadán třetí parametr - šablona pro případ prázdných dat,
     * použije tuto šablonu.
     *
     * Data musí být iterovatelná příkazem foreach, musí tedy být typu iterable. To platí i pro prázdná data, například lze použít prázdné pole. Obvykle se jedná o asociativní pole.</p><p>
     * Data musí být iterable data s položkami klíč-hodnota (například asociativní pole) s retězcovými (nečíselnými) klíči. </p><p>
     * Pak se šablona renderuje tak, že jsou v ní k dispozici jednotlivé položky dat extrahované do proměnných se jmény odpovídajícími klíčům dat.
     * Z toho také plyne zákaz číselnýcj indexů - z číselného indexu by nevznikla platná php proměnná (např. $12) a takpvý prvek pole pak není
     * v šabloně nijak dostupný.</p><p>
     *
     * @param string $templateFilename Plné jméno souboru s php šablonou
     * @param iterable $data Iterovatelná data.
     * @param type $emptyDataTemplateFilename
     */
    public function insert($templateFilename, iterable $data=[], $emptyDataTemplateFilename='');

    /**
     * <p>Pokud jsou zadána data iteruje data, při každé iteraci vloží šablonu a renderuje ji s použitím jedné položky těchto dat.</p><p>
     * Data musí být iterovatelná příkazem foreach, musí tedy být typu iterable. Obvykle se jedná o číslované pole (ne asociativní pole,
     * asociativní pole je možno použít, ale hodnoty klíčů se nijak neprojeví. </p><p>
     * Jednotlivé položky dat mohou být:
     * <ul>
     * <li><p>iterable data s položkami klíč-hodnota (například asociativní pole) s retězcovými (nečíselnými) klíči. </p><p>
     * Pokud položky jsou iterable data s položkami klíč-hodnota (například asociativní pole) s retězcovými (nečíselnými) klíči, pak se opakovaně vkládaná šablona renderuje tak,
     * že jsou v ní k dispozici jednotlivé položky tšchto dat extrahované do proměnných se jmény odpovídajícími klíčům dat.
     * Z toho také plyne zákaz číselnýcj indexů - z číselného indexu nevznikne platná php proměnná (např. $12) a takpvý prvek pole pak není
     * v šabloně nijak dostupný.</p><p>
     * </li><li><p> data skalárního typu (typicky string nebo převoditelná na string).</p><p>
     * Pokud položka pole dat je skalár, pak je v šabloně dostupná se jménem zadaným jako třetí parametr metody repeat ($variableName). </p><p>
     * </ul>
     * <p>
     * Pokud jsou zadána prázdná data a je zadán třetí parametr - šablona pro případ prázdných dat, použije tuto šablonu. </p><p>
     * Pokud jsou zadána prázdná data a není zadán třetí parametr, může vracet prázdný řetězec nebo pevnou náhradní hodnotu. </p>
     *
     * @param string $templateFilename Plné jméno souboru s šablonou
     * @param iterable $data
     * @param string $emptyDataTemplateFilename Plné jméno souboru s šablonou pro prázdná data
     */
    public function repeat($templateFilename, iterable $data=[], $variableName='item', $emptyDataTemplateFilename='');

    /**
     * Pokud je zadaná podmínka vyhodnocena jako true, vloží šablonu, pokud jsou zadána data renderuje ji s použitím těchto dat. Pokud data nejsou zadána a je zadán třetí parametr - šablona pro případ prázdných dat,
     * použije tuto šablonu.
     *
     * Data musí být iterovatelná příkazem foreach, musí tedy být typu iterable. Obvykle se jedná o číslované pole (ne asociativní pole,
     * asociativní pole je možno použít, ale hodnoty klíčů se nijak neprojeví. </p><p>
     * Jednotlivé položky dat musí být iterable data s položkami klíč-hodnota (například asociativní pole) s retězcovými (nečíselnými) klíči. </p><p>
     * Pokud položka pole dat je asociativní pole s retězcovými (nečíselnými) indexy, pak se opakovaně vkládaná šablona renderuje tak,
     * že jsou v ní k dispozici jednotlivé položky tohoto asociativnímo pole extrahované do proměnných se jmény odpovídajícími indexům pole.
     * Z toho také plyne zákaz číselnýcj indexů - z číselného indexu nevznikne platná php proměnná (např. $12) a takpvý prvek pole pak není
     * v šabloně nijak dostupný.</p><p>
     *
     * @param bool $condition
     * @param string $templateFilename
     * @param iterable $data
     * @param string $emptyDataTemplateFilename
     */
    public function insertConditionally($condition=false, $templateFilename, iterable $data=[], $emptyDataTemplateFilename='');
}
