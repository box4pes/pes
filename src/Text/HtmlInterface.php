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
interface HtmlInterface {

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako iterable proměnnou s dvojicemi key=>value.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Pro atributy s hodnotou typu boolean generuje jen jméno parametru (standard html nikoli xml)</li>
     * <li>Pro atributy s hodnotou typu array generuje dvojici jméno="řetězec hodnot oddělených mezerou", řetězec hodnot vytvoří zřetězením hodnot v poli oddělených mezerou a obalí uvozovkami</li>
     * <li>Ostatní atributy jako dvojici jméno="hodnota" s tím, že hodnotu prvku převede na string a obalí uvozovkami.</li>
     * </ul>
     * Pokud je hodnota atributu řetězec, který obsahuje uvozovky, výsledné html bude chybné. Hodnota atributu je vždy obalena uvozovkami.
     * Výsledný navrácený řetězec začíná mezerou a atributy v řetězci jsou odděleny mezerami.
     *
     * Příklad:
     * ['id'=>'alert', 'class'=>['ui', 'item', 'alert'], 'readonly'=>TRUE, data-info=>'a neni b'] převede na: id="alert" class="ui item alert" readonly data-info="a neni b".
     * Víceslovné řetězce (typicky class) lze tedy zadávat jako pole nebo víceslovný řetězec.
     *
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value.
     * @return string
     */
    public static function attributes(iterable $attributes);

    /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value.
     * @param string $innerHtml Text, bude bez úprav vložen jako textový obsah tagu
     * @return string
     */
    public static function tag($name, iterable $attributes=[], $innerHtml='');

    /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value.
     * @return string
     */
    public static function tagNopair($name, iterable $attributes=[]);

    /**
     * Převede text s dvakrát odřádkovanými odstavci na html paragrafy (<p></p>)
     * Vstupní text obalí na začátku a na konci otevíracím tagem <p> a koncovým tagem </p>,
     * Výskyty dvou odřádkování uvnitř textu chápe jako konec odstavce a z každého takto odděleného úseku textu vytvoří paragraf.
     * Jednoho odřádkování v textu si nijak nevšímá, váš vstupní text můžete jedním odřádkováním zalamovat libovolně, např. proto, aby byl vidět ve vašem editoru.
     * Chcete-li skutečně vytvořit odstavec, použijte dvojí odřádkování.
     *
     * Metoda nijak nemění jakékoli html značky (tagy) ani žádné viditelné znaky v textu, naopak mění odřádkování (CR, LF alias \r, \n) a whitespaces (mezery, tabelátory ad.).
     *
     * @param type $text
     * @return string
     */
    public static function p($text);

    /**
     * Generuje html kód tagu select včetně tagů option. Pokud je zadán parametr label, přidá tag label svázaný s generovaným tagem select.
     *
     * Pokud je zadán parametr label, parametr attributes by měl obsahovat položku s klíčem "id", pokud ji neobsahuje, bude doplněna.
     * Parametr attributes může obsahovat položku s klíčem "name", ale ta nebude použita.
     *
     * Pokud je zadán parametr label a parametr attributes neobsahuje položku "id" je jako fallback vygenerováno id jako náhodný řetězec (uniquid).
     * Pro propojení generovaného tagu label použito zadané případně vygenerované id.
     * Pokud parametr attributes obsahuje položku "name", nepoužije se (přednost má povinný parametr name).
     *
     * Vygenerovaný option se stejnou hodnotou jako je hodnota položky kontextu s klíčem odpovídajícím parametru name je doplněn atributem selected.
     *
     * @param string $name
     * @param string $label
     * @param iterable $optionValues Hodnoty pro generování tagů option - iterable proměnná s dvojicemi key=>value.
     * @param array $context
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value.
     */
    public static function select($name, $label='', iterable $optionValues=[], array $context=[], iterable $attributes=[]);

    /**
    /**
     * Generuje htm kód tagu input. Pokud je zadán parametr label, přidá tag label svázaný s generovaným tagem input.
     *
     * Atributy name a value vždy generuje z hodnot parametrů.
     *
     * Pokud je zadán parametr label a parametr attributes neobsahuje položku "id" je jako fallback vygenerováno id jako náhodný řetězec (uniquid).
     * Pro propojení generovaného tagu label použito zadané případně vygenerované id.
     *
     * Pokud parametr attributes obsahuje položku "name", nepoužije se (přednost má povinný parametr name).
     *
     * Atribut value je generován tak, že jako hodnota je použita hodnota položky kontextu s klíčem odpovídajícím parametru name, případně prázný řetězec.
     * Pokud parametr attributes obsahuje položku "value", nepoužije se, přednost hodnota v poli context s klíčem odpovídajícím parametru name).
     *
     * @param type $name
     * @param type $label
     * @param array $context
     * @param iterable $attributes
     */
    public static function input($name, $label='', array $context=[], iterable $attributes=[]);
}
