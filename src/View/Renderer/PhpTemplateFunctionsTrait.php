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
 * PhpTemplateTrait obsahuje metody, tzv. filtry, které lze volat v html/php šablonách uvením php kódu $this->metoda(), jsou určeny
 * pro tvůrce šablony a slouží jako pomocné metody pro generování textu z šablony. Obvykle se takovým metodám v template systémech
 * říká filtry.
 *
 * Zde se využívá skutečnosti, že v okamžiku renderování šablony se šablona provádí jako php kód příkazem include
 * uvnitř metody PhpTemplate->protectedIncludeScope() a v tu chvíli jsou z kódu šablony viditelné a spustitelné
 * všechny metody objektu PhpTemplate.
 *
 * @author pes2704
 */
trait PhpTemplateFunctionsTrait {

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
    public function filter($filters='', $text='') {
        $names = explode(self::FILTER_DELIMITER, $filters);
        foreach ($names as $name) {
            if (method_exists($this, $name)) {
                $text = $this->$name($text);
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
    public function e($text='') {
        return $this->esc($text);
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
     * pozor tedy také na pořadí filtrovacích metod: $this->filter('e|mono', 'v neděli'); je v pořádku, zatímco $this->filter('mono|e', 'v neděli');
     * oescapuje i &nbsp; vytvořené filtrem "mono".
     *
     * @param type $text
     * @return type
     */
    public function esc($text='') {
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
    public function mono($text='') {
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
    public function p($text='', $attributes=[]) {
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
            $text .= $this->tag("p", $attributes, trim($chunk));  // původně bylo $text .= '<p>' . trim($chunk, "\n") . "</p>\n";
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
    public function nl2br($text='') {
        return str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
    }

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javarcript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public function esc_js($text='') {
        $safe_text = htmlspecialchars( $text, ENT_COMPAT );   // konvertuje &"<> na &xxx kódy (ENT_COMPAT Will convert double-quotes and leave single-quotes alone.)
        $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );  // stripslashes odstraní escapovací zpětná lomítka (vždy jedno), preg vymění &#x27; a &#039; (oboje apostrofy) za apostrof

        return $safe_text;
    }

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako asociativní pole.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Atributy s logickou hodnotou uvede jen jako jméno parametru (standard html nikoli xml)</li>
     * <li>Atributy s hodnotou typu array jako dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotu prvku přčevede na string a obalí uvozovkami.</li>
     * </ul>
     * Pokud je hodnota atributu řetězec, který obsahuje uvozovky, výsledné html bude chybné. Hodnota atributu je vždy obalena uvozovkami.
     * Výsledný navrácený řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['id'=>'alert', 'class'=>['ui', 'item', 'alert'], 'readonly'=>TRUE, data-info=>'a neni b'] převede na: id="alert" class="ui item alert" readonly data-info="a neni b".
     * Víceslovné řetězce (typicky class) lze tedy zadávat jako pole nebo víceslovný řetězec.
     * @param array $attributesArray Asocitivní pole
     * @return string
     */
    public function attributes($attributesArray=[]) {
        foreach ($attributesArray as $type => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attr[] = $type;
                }
            } else {
                $attr[] = $type.'="'.$value.'"';
            }
        }
        return isset($attr) ? ' '.implode(' ',$attr) : '';
    }

    /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @param string $innerHtml Text, bude bez úprav vložen jako textový obsah tagu
     * @return string
     */
    public function tag($name, array $attributes=[], $innerHtml='') {
        if ($name) {
            $html = "<$name ".$this->attributes($attributes).">".PHP_EOL.$innerHtml.PHP_EOL."</$name>";
        }
        return $html ?? '';
    }

    /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param array $attributes Asociativní pole. Viz metoda attributes().
     * @return string
     */
    public function tagNopair($name, array $attributes=[]) {
        if ($name) {
            $html = "<$name ".self::attributes($attributes)." />";
        }
        return $html ?? '';
    }
}
