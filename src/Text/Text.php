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
    public static function filter(string $filters, string $text): string {
        $names = explode(self::FILTER_DELIMITER, $filters);
        foreach ($names as $name) {
            if (array_key_exists($name, get_class_methods(self::class))) {
                $text = self::$name($text);
            }
        }
        return $text;
    }

    /**
     * Nedělitelné mezery pro český text. Zamezí zalamování textu za jednoznakovou předložkou, spojkou a mezi čísly datumu pro český text.
     * <ul>
     * <li>Metoda nahradí mezeru mezi jednoznakovou předložkou nebo spojkou a následujícím slovem nedělitelnou mezerou.
     * Jednoznakové předložky a spojky jsou: k, s, v, z, o, u, i, a.</li>
     * <li>Metoda nahradí mezeru mezi číslem zakončeném tečkou a dalším číslem nedělitelnou mezeru</li>
     * <ul>
     *
     * Metoda nepracuje správně v případě více než jedné jednoznakové spojky nebo předložky za sebou, např, text 'a i s jinými' převede jen na 'a&nbsp;i s&nbsp;jinými'.
     *
     * @param type $text
     * @return type
     */
    public static function mono(string $text): string {
        $patterns = [
            '/(\s[ksvzouiaKSVZOUIA])\s/',
            '/(\d{1})\.\s(\d{1})\.\s(\d{1})/',
            '/(\d{1})\.\s(\d{1})/'
        ];
        $replacements = [
           '$1&nbsp;',
           '$1.&nbsp;$2.&nbsp;$3',
           '$1.&nbsp;$2'
        ];
        return preg_replace($patterns, $replacements, trim($text));
    }

    /**
     * Řetěcec datumu v českém formátu normalizuje na tvar den. měsíc. rok, čísla bez levostraných nul a s tečkou (pořadová) a mezi tečkou a následujícím číslem obyčejná mezera.
     *
     * @param string $dateCsFormatted
     */
    public static function dateCsSpaces(string $dateCsFormatted): string  {
        return \implode('. ', self::dateCsTokens($dateCsFormatted));
    }

    /**
     * Řetěcec datumu v českém formátu normalizuje na tvar den. měsíc. rok, čísla bez levostraných nul a s tečkou (pořadová) a mezi tečkou a následujícím číslem
     * nerozděkitelná HTML mezera - řetězec &nbsp;
     *
     * @param string $dateCsFormatted
     */
    public static function dateCsNbsp(string $dateCsFormatted): string  {
        return \implode('.&nbsp;', self::dateCsTokens($dateCsFormatted));
    }

    /**
     * Z datumu v českém formátu vytvoří pole obsahujíci jednotlivá čísla datumu bez mezer a levostraných nul
     * @param type $dateCsFormatted
     * @return array
     */
    private static function dateCsTokens(string $dateCsFormatted) {
        // odstraní whitespaces
        $tokens = explode('.', preg_replace('/\s/', '', $dateCsFormatted));
        foreach ($tokens as $key=>$value) {
            $tokens[$key] = (string) (int) $value;
        }
        return $tokens;
    }

    /**
     * Převede odřádkování v textu na značku (tag) <br />. Převádí každé odřádkování, vícenásobné odřádkování způsobí vícenásobné vložení značky.
     *
     * @param type $text
     * @return type
     */
    public static function nl2br(string $text): string {
        return str_replace(array("\r\n", "\r", "\n"), "<br />", $text);
    }

    /**
     *
     * @param bool $condition Default true
     * @param string $textOnTrue
     * @param string $textOnFalse Nepoviný parametr
     * @return string
     */
    public static function resolve($condition = true, string $textOnTrue, string $textOnFalse = ''): string {
        if ((bool) $condition) {
            return $textOnTrue;
        } else {
            return $textOnFalse;
        }
    }

#### ESCAPE ############################

    /**
     * Alias k metodě esc().
     *
     * @param string $text
     * @param int $flags Kombinace (logický součet) PHP konstant ENT_XXXXXX, defaultně ENT_NOQUOTES | ENT_HTML5 | ENT_SUBSTITUTE
     * @param bool $doubleEncode Defaultně false
     * @return type
     */
    public static function e(string $text): string {
        return self::esc($text);
    }

    /**
     * Metodu použijte na ošetření textů, které obsahují text zadaný uživatelem (např. ve formuláři). Jde o základní ochranu proti XSS útokům.
     * Metoda provede tzv escapování. Všechny znaky, které mohou v HTML mít význam, tzv. rezervované znaky HTML, převede na HTML entity.
     * Např. znak < na &lt; apod.
     *
     * Metoda interně používá htmlspecialchars() s parametry:
     * flags: Kombinace (logický součet) PHP konstant ENT_NOQUOTES | ENT_HTML5 | ENT_SUBSTITUTE
     * encoding: UTF-8
     * doubleEncode: false (opačně než htmlspecialchars())
     *
     * Metoda předpokládá, že text je HTML5 a zachovává všechny znaky povolené v HTML5, neescapuje uvozovky ani apostrofy, nahrazuje invalidní části textu znaky
     * Unicode Replacement Character U+FFFD (UTF-8). Metoda neescapuje nalezené HTML5 entity.
     *
     * Metoda pracuje pouze s kódováním UTF-8.
     *
     * Metoda nestačí na ošetření textů vkládaných kamkoli jinam než jen do textového obsahu tagu určeného k zobrazení. Nebrání ani XSS útoku na atributy tagu.
     * Nikdy nevkládejte uživatelem zadaný text do obsahu tagu <script>, do html komentáře <!-- -->, do názvu atributu, do jména tagu, do css.
     * Tato místa nelze nikdy dokonale ošetřit.
     *
     * Tato metoda neescapuje html entity, které byly ve vstupním textu, např. pokud text obsahuje "<mluví> o&nbsp;všem" zachová vznikne "&lt;mluví&gt; o&amp;nbsp;všem".
     * Díky tomu nezáleží na pořadí filtrovacích metod: self::filter('e|mono', 'v neděli'); je v pořádku, self::filter('mono|e', 'v neděli'); také,
     * neescapuje &nbsp; vytvořené filtrem "mono".
     *
     * @param string $text
     * @param int $flags Kombinace (logický součet) PHP konstant ENT_XXXXXX, defaultně ENT_NOQUOTES | ENT_HTML5 | ENT_SUBSTITUTE
     * @param bool $doubleEncode Defaultně false
     * @return type
     */
    public static function esc(string $text): string {
        // https://www.php.net/manual/en/function.htmlspecialchars.php#125979
        return htmlspecialchars( $text, ENT_NOQUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8', false );
    }

    public static function esc_attr(string $text): string {
        // https://www.php.net/manual/en/function.htmlspecialchars.php#125979
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8', false );
    }

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javascript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public static function esc_js(string $text): string {
        $safe_text = htmlspecialchars( $text, ENT_COMPAT );   // konvertuje &"<> na &xxx kódy (ENT_COMPAT Will convert double-quotes and leave single-quotes alone.)
        $safe_text = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes( $safe_text ) );  // stripslashes odstraní escapovací zpětná lomítka (vždy jedno), preg vymění &#x27; a &#039; (oboje apostrofy) za apostrof
        $safe_text = str_replace( "\r", '', $safe_text );
	$safe_text = str_replace( "\n", '\\n', addslashes( $safe_text ) );
        return $safe_text;
    }
    
    /**
     * Enkóduje celou url path (obsahující lomítka) podle  RFC 3986 (nahrazuje zakázané znaky v url k=odováním %XX).
     * 
     * @param string $path
     * @return string
     */
    public static function encodeUrlPath(string $path): string {
        return implode('/', array_map(function ($v) {return rawurlencode($v);}, explode('/', $path)));
    }

}
