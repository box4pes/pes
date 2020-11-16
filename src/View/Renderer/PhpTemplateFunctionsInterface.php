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
    public function filter($filters, $text);

    /**
     * Alias k metodě esc().
     *
     * @param type $text
     * @return type
     */
    public function e($text);

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
    public function esc($text);

    /**
     * Jednoznakové předložky a spojky pro český text. Metoda vloží mezi jednoznakové předložky nebo spojky a následující slovo nedělitelnou mezeru.
     * Jednoznakové předložky a spojky jsou: k, s, v, z, o, u, i, a.
     *
     * @param type $text
     * @return type
     */
    public function mono($text);

    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (<p></p>)
     * Vstupní text obalí na začátku a na konci otevíracím tagem <p> a koncovým tagem </p>,
     * Výskyty dvou odřádkování uvnitř textu chápe jako konec odstavce a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte dvojí odřádkování.
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     * @param type $text
     * @return string
     */
    public function p($text);

    /**
     * Převede odřádkování v textu na značku (tag) <br />. Převádí každé odřádkování, vícenásobné odřádkování způsobí vícenásobné vložení značky.
     *
     * @param type $text
     * @return type
     */
    public function nl2br($text);

    /**
     * Escapuje retězec, který obsahuje javascript, který má být vložen jako inline javarcript do atributu onclick, onblur atd.
     *
     * @param string $text
     */
    public function esc_js($text='');

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako asociativní pole.
     * - Atributy s logickou hodnotou: generuje pro hodnotu TRUE jen jméno parametru, pro hodnotu FALSE nic
     * - ostatní atributy: generuje dvojici jméno="hodnota" s tím, že hodnotu prvku obalí uvozovkami. Výsledný
     * řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['class'=>'ui item alert', 'readonly'=>TRUE, ] převede na: class="ui item alert" readonly
     * ['class'=>'ui item ok', 'readonly'=>FALSE, ] převede na: class="ui item ok"
     *
     * @param array $attributesArray Asocitivní pole
     * @return string
     */
    public function attributes($array);
}
