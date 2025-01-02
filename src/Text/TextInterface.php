<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Text;

/**
 *
 * @author pes2704
 */
interface TextInterface {

    /**
     * Metoda použije postupně zleva jednotlivé filtry uvedené jako posloupnost názvů filtrů oddělených znakem FILTER_DELIMITER.
     *
     * Příklad:
     * Pro FILTER_DELIMITER = '|' volání $this->(e|mono|p, 'Text, který bude filtrován.') způsobí postupné volání filtru (metody)
     * e(), mono(), p(). Ekvivalentně zápisu:
     * <pre>
     * $this->p($this->mono($this->e('Text, který bude filtrován.')));
     * </pre>
     *
     * @param type $filters
     * @param type $text
     * @return type
     */
    public static function filter(string $filters, string $text): string;

    /**
     * Jednoznakové předložky a spojky pro český text. Metoda vloží mezi jednoznakové předložky nebo spojky a následující slovo nedělitelnou mezeru.
     * Jednoznakové předložky a spojky jsou: k, s, v, z, o, u, i, a.
     *
     * @param type $text
     * @return type
     */
    public static function mono(string $text): string;

    /**
     * Řetěcec datumu v českém formátu normalizuje na tvar den. měsíc. rok, čísla bez levostraných nul a s tečkou (pořadová) a mezi tečkou a následujícím číslem obyčejná mezera.
     *
     * @param string $dateCsFormatted
     */
    public static function dateCsSpaces(string $dateCsFormatted): string;

    /**
     * Řetěcec datumu v českém formátu normalizuje na tvar den. měsíc. rok, čísla bez levostraných nul a s tečkou (pořadová) a mezi tečkou a následujícím číslem
     * nerozděkitelná HTML mezera - řetězec &nbsp;
     *
     * @param string $dateCsFormatted
     */
    public static function dateCsNbsp(string $dateCsFormatted): string;

    /**
     * Převede odřádkování v textu na značku (tag) <br />. Převádí každé odřádkování, vícenásobné odřádkování způsobí vícenásobné vložení značky.
     *
     * @param type $text
     * @return string
     */
    public static function nl2br(string $text): string;

    /**
     *
     * @param bool $condition
     * @param string $textOnTrue
     * @param string $textOnFalse
     * @return string
     */
    public static function resolve($condition = true, string $textOnTrue, string $textOnFalse = ''): string;

    /**
     * Alias k metodě esc().
     *
     * @param string $text
     * @return type
     */
    public static function e(string $text): string;

    /**
     * Metoda musí nahrazovat znaky způsobem vhodným pro použítí výsledného textu v HTML5.
     *
     * @param string $text Text k escapování
     */
    public static function esc(string $text): string;

    public static function esc_attr(string $text): string;

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javarcript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public static function esc_js(string $text): string;
    
    /**
     * Enkóduje celou url path (obsahující lomítka) podle  RFC 3986 (nahrazuje zakázané znaky v url k=odováním %XX).
     * 
     * @param string $path
     * @return string
     */
    public static function encodeUrlPath(string $path): string;
}
