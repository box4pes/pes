<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Text;

/**
 * Obsahuje statické metody, které jsou určeny jako pomocné metody pro transformování textu.
 * Obvykle se takovým metodám v template systémech říká filtry. Obsahuje jednotlivé filtry a metodu filter(),
 * která umožňuje setavit složený filtr.
 *
 * @author pes2704
 */
class Text implements TextInterface {

    const FILTER_DELIMITER = "|";

    /**
     * Metoda použije postupně zleva jednotlivé filtry uvedené jako posloupnost názvů filtrů oddělených znakem FILTER_DELIMITER.
     *
     * Příklad:
     * Pro FILTER_DELIMITER = '|' volání self::filter(e|mono|p, 'Text, který bude filtrován.') způsobí postupné volání filtru (metody)
     * e(), mono(), p(). Ekvivalentně zápisu:
     * <pre>
     * self::p(self::mono(self::e('Text, který bude filtrován.')));
     * </pre>
     *
     * @param type $filters
     * @param type $text
     * @return type
     */
    public static function filter($filters='', $text='') {
        $names = explode(self::FILTER_DELIMITER, $filters);
        foreach ($names as $name) {
            if (array_key_exists($name, get_class_methods(self::class))) {
                $text = self::$name($text);
            }
        }
        return $text;
    }

    /**
     * Alias k metodě esc().
     *
     * @param type $text
     * @return type
     */
    public static function e($text='') {
        return self::esc($text);
    }

    /**
     * Metodu použijte na ošetření textů, které obsahují text zadaný uživatelem (např. ve formuláři). Jde o základní ochranu proti XSS útokům.
     * Metoda provede tzv escapování. Všechny znaky, které mohou v HTML mít význam, tzv. rezervované znaky HTML, převede na HTML entity.
     * Např. znak < na &lt; apod.
     * Metoda nestačí na ošetření textů vkládaných kamkoli jinam než jen do textového obsahu tagu určeného k zobrazení.
     * Nikdy nevkládejte uživatelem zadaný text do obsahu tagu <script>, do html komentáře <!-- -->, do názvu atributu, do jména tagu, do css.
     * Tato místa nelze nikdy dokonale ošetřit.
     *
     * Tato metoda escapuje i html entity, které byly v opravovaném textu, např. pokud text obsahuje "mluví o&nbsp;všem" vznikne "mluví o&amp;nbsp;všem".
     * pozor tedy také na pořadí filtrovacích metod: self::filter('e|mono', 'v neděli'); je v pořádku, zatímco self::filter('mono|e', 'v neděli');
     * oescapuje i &nbsp; vytvořené filtrem "mono".
     *
     * @param type $text
     * @return type
     */
    public static function esc($text='') {
        return htmlspecialchars($text);
    }

    /**
     * Nedělitelné mezery pro český text. Zamezí zalamování textu za jednoznakovou předložky, spojkou a mezi čísly datumu pro český text.
     * <ul>
     * <li>Metoda vloží mezi jednoznakové předložky nebo spojky a následující slovo nedělitelnou mezeru.
     * Jednoznakové předložky a spojky jsou: k, s, v, z, o, u, i, a.</li>
     * <li>Metoda vloží mezi číslo zakončené tečkou a další číslo nedělitelnou mezeru</li>
     * <ul>
     *
     * @param type $text
     * @return type
     */
    public static function mono($text='') {
        $patterns = [
            '/(\s[ksvzouiaKSVZOUIA])\s/',
            '/(\d{1})\.\s(\d{1})/'
        ];
        $replacements = [
           '$1&nbsp;',
             '$1.&nbsp;$2'
        ];
        return preg_replace($patterns, $replacements, trim($text));
    }

    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (tagy p). Umožňuje zadat atributy vytvážených tagů p (např. class pro styly).
     *
     * Ze všech úseků textu vytvoří html paragrafy, vloží tyto úseky jako obsah tagu p.
     * Výskyty dvou odřádkování uvnitř textu chápe jako konec úseku a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte v textu dvojí odřádkování.
     *
     * Vytvářené tagy p mohu být vytvářeny s atributy. Atributy jsou zadány nepoviným parametrem $attributes ve formě asociativního pole. Viz metoda attributes().
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     * @param string $text Vstupní text
     * @param array $attributes Nepoviný parametr. Atributy vytvářených tagů p zadané jako asociativní pole. Viz metoda attributes().
     * @return string
     */
    public static function p($text='', $attributes=[]) {
        // kopie z https://core.trac.wordpress.org/browser/trunk/src/wp-includes/formatting.php
        //
        if ( trim($text) === '' )
        return '';

        // Just to make things a little easier, pad the end.
//        $text = $text . "\n";
        // Standardizuje odřádkování na \n
        $text = str_replace(array("\r\n", "\r"), "\n", $text);
        // Odstraní více než dvě odřádkování za sebou
        $text = preg_replace("/\n\n+/", "\n\n", $text);
        // Rozdělí na kousky, separátor jsou dvě odřádkování (mezi může být libovolný počet whitespaces)
        $chunks = preg_split('/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY);  //PREG_SPLIT_NO_EMPTY - preg_split vrací jen neprázné kousky

        // Složí text z kousků obalených počátečním a koncovým <p>
        $text = '';
        foreach ( $chunks as $chunk ) {
            $text .= self::tag("p", $attributes, trim($chunk)).self::EOL;  // původně bylo $text .= '<p>' . trim($chunk, "\n") . "</p>\n";
        }
        // Under certain strange conditions it could create a P of entirely whitespace.
        $text = preg_replace('|<p>\s*</p>|', '', $text);
        return $text;
    }

    /**
     * Převede odřádkování v textu na značku (tag) <br />. Převádí každé odřádkování, vícenásobné odřádkování způsobí vícenásobné vložení značky.
     *
     * @param type $text
     * @return type
     */
    public static function nl2br($text='') {
        return str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
    }

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javascript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public static function esc_js($text='') {
        $safe_text = htmlspecialchars( $text, ENT_COMPAT );   // konvertuje &"<> na &xxx kódy (ENT_COMPAT Will convert double-quotes and leave single-quotes alone.)
        $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );  // stripslashes odstraní escapovací zpětná lomítka (vždy jedno), preg vymění &#x27; a &#039; (oboje apostrofy) za apostrof

        return $safe_text;
    }
}
