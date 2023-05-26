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

use Pes\Text\Text;

/**
 * Obsahuje statické metody, které generují html string.
 *
 * Jedná se o klon PhpTemplateTrait.
 *
 * @author pes2704
 */
class Html implements HtmlInterface {

    const EOL = PHP_EOL;

#### value escaper #######

    static $escaper;

    public static function setEscaper(callable $escaper) {
        self::$escaper = self::$escaper;
    }

    private static function value() {
        if (isset(self::$escaper)) {
            return self::$escaper($value);
        } else {
            return Text::esc($value);
        }
    }

##########################

    /**
     * Metoda generuje textovou reprezentaci atributů html tagu z dat zadaných jako iterable proměnnou s dvojicemi key=>value.
     *
     * Jednotlivé hodnoty atrubutů vždy uzavírá do uvozovek. Tím se zmenšuje riziko XSS útoku kódem v atributech. Přesto nevkládejte uživatelský vstup do hodnot atributů.
     *
     * Podle typu hodnoty atributu:
     * <ul>
     * <li>Pro atributy s hodnotou typu boolean s hodnotou true generuje jen jméno parametru (standard html nikoli xml), s hodnotou false negeneruje nic.</li>
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
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value
     * @return string
     */
    public static function attributes(iterable $attributes=[]) {
        foreach ($attributes as $type => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attr[] = $type;  // jen pro true
                }
            } elseif (is_array($value)) {
                $attr[] = $type.'="'.implode(' ', $value).'"';
            } else {
                $attr[] = $type.'="'.trim((string) $value).'"';
            }
        }
        return isset($attr) ? implode(' ',$attr) : '';
    }

    /**
     * Generuje html kód párového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value
     * @param variadic $innerTag Sada proměnných, které budou vloženy jako textový obsah tagu, jednotlivé proměnné mohou být typu string nebo pole
     * @return string
     */
    public static function tag($name, iterable $attributes=[], ...$innerTag) {
        $html = [];
        if ($name) {
            $attr = self::attributes($attributes);
            if ($innerTag) {
                $innerHtml = Html::implodeInnerTags($innerTag);
                $ret = "<$name".($attr ? " $attr" : '').">".$innerHtml."</$name>";
            } else {
                $ret = "<$name".($attr ? " $attr" : '')."></$name>";
            }
        }
        return $ret ?? '';
    }

    /**
     *
     * @param array $innerTag
     * @return type
     */
    private static function implodeInnerTags(array $innerTag) {
        $innerHtml = [];
        foreach ($innerTag as $value) {
            if (is_array($value)) {
                if (count($value)>0) {
                    $innerHtml[] = Html::implodeInnerTags($value);
                } else {
                    $innerHtml[] = "";
                }
            } else {
                $innerHtml[] = (string) $value;
            }
        }
        if (count($innerHtml)>1) {
            $impl = self::EOL.implode(self::EOL, $innerHtml).self::EOL;
        } else {
            $impl = $innerHtml[0];
        }
        return $impl;
    }

    /**
     * Generuje html kód nepárového tagu.
     *
     * @param string $name Jméno tagu. Bude použito bez změny malách a velkých písmen
     * @param iterable $attributes Asociativní pole. Viz metoda attributes().
     * @return string
     */
    public static function tagNopair($name, iterable $attributes=[]) {
        if ($name) {
            $attr = self::attributes($attributes);
            $html = "<$name".($attr ? " $attr" : '')." />";
        }
        return $html ?? '';
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
     * Generuje htm kód tagu input a případně i tagu label.
     * <p><b>Tag label:</b></p>
     * <p>Pokud je zadán parametr <code>label</code>, metoda přidá tag label a generovaný tag input vloží do tagu label jako potomka.
     * Label je zobrazován vlevo před tagem input a kliknutí na label přesune focus formuláře na input.
     * Pokud atributy pro input <code>inputAttributes</code> obsahují položku "id" a je zadán parametr <code>label</code> je k atributům tagu label přidána položka "for" s hodnotou id inputu.</p>
     * <p><b>Atribut type:</b></p>
     * <p>Pokud atributy pro tag input <code>inputAttributes</code> neobsahují položku "type" použije se typ "text".</p>
     * <p><b>Atributy name a value:</b></p>
     * <p>Atributy name a value tagu input vždy generuje z hodnot parametrů takto:</p>
     * <ul>
     * <li>Jako hodnota atributu name tagu inpu se vždy použije parametr <code>name</code>. tPokud parametr <code>inputAttributes</code> obsahuje položku "name", tato hodnota se nepoužije (přednost má povinný parametr <code>name</code>).</li>
     * <li>Jako hodnota atributu value je použita hodnota položky parametru <code>context</code> se jménem odpovídajícím parametru <code>name</code>, případně prázný řetězec.
     * Pokud parametr <code>inputAttributes</code> obsahuje položku "value", tato hodnota se nepoužije, přednost má hodnota v poli položky parametru <code>context</code> se jménem odpovídajícím parametru <code>name</code>).</li>
     * </p>
     * @param string $name
     * @param string $label
     * @param array $context Pole kontext - asociativní pole dvojic name=>value.
     * @param iterable $inputAttributes
     * @return type
     */
    public static function input($name, $label='', array $context=[], iterable $inputAttributes=[], iterable $labelAttributes=["style"=>"white-space:nowrap"]) {
        if (!array_key_exists("type", $inputAttributes)) {
            $inputAttributes["type"] = "text";
        }
        $inputAttributes["name"] = $name;
        $inputAttributes["value"] = array_key_exists($name, $context) ? $context[$name] : '';

        if($label ?? '') {
            if(isset($inputAttributes["id"]) AND $inputAttributes["id"]) {
                $labelAttributes["for"] = $inputAttributes["id"];
            }
            $html[] = Html::tag("label", $labelAttributes,
                    $label
                    .
                    Html::tagNopair("input", $inputAttributes)
                );
        } else {
            $html[] = Html::tagNopair("input", $inputAttributes);
        }
        return implode(PHP_EOL, $html);
    }

    public static function textarea($name, $label='', array $context=[], iterable $attributes=[]) {
        if ($label AND !array_key_exists("id", $attributes)) {
            $attributes["id"] = uniqid();
        }
        $attributes["name"] = $name;
        $innertext = array_key_exists($name, $context) ? $context[$name] : '';

        if ($label) {
            $html[] = Html::tag("label", ["for"=>$attributes["id"]], $label);
        }
        $html[] = Html::tag('span', [],
                    Html::tag("textarea", $attributes, $innertext)
                );
        return implode(PHP_EOL, $html);
    }

    /**
     * Generuje html kód tagu select včetně tagů option. Pokud je zadán parametr label, přidá tag label svázaný s generovaným tagem select.
     *
     * Generování label:
     * Pokud je zadán parametr label, parametr attributes by měl obsahovat položku s klíčem "id", pokud ji neobsahuje, bude doplněna.
     * Parametr attributes může obsahovat položku s klíčem "name", ale ta nebude použita.
     *
     * Pokud je zadán parametr label a parametr attributes neobsahuje položku "id" je jako fallback vygenerováno id jako náhodný řetězec (uniquid).
     * Pro propojení generovaného tagu label použito zadané případně vygenerované id.
     * Pokud parametr attributes obsahuje položku "name", nepoužije se (přednost má povinný parametr name).
     *
     * Generování option:
     * Hodnoty pro generování tagů option - iterable proměnná s dvojicemi key=>value. Hodnoty $key jsou použity jako hodnota proměnné formuláře (value v tagu option),
     * $value jsou použity pro zobrazení v html (html obsah tagu option).
     * Pokud je jako parametr použito neasociativní pole (automaticky číslované) dojde k tomu, že číselné klíče se nepoužijí a hodnoty pole bodou použity jako value v option i jako zobrazované hodnoty v html.
     *
     * Pokud je v kontextu položka se jménem odpovídajícím parametru name pak vygenerovaný option se stejnou hodnotou jako je hodnota položky je doplněn atributem selected.
     *
     * @param string $name Jméno proměnné formuláře (má přednost před případným atributem name)
     * @param string $label Pokud je zadán vygeneruje se tag label
     * @param iterable $optionValues Hodnoty pro generování tagů option - iterable proměnná s dvojicemi key=>value.
     * @param array $context Kontext - asociativní pole dat získaných z formuláře.
     * @param iterable $attributes Atributy - iterable proměnná s dvojicemi key=>value.
     * @return string
     */
    public static function select($name, $label='', iterable $optionValues=[], array $context=[], iterable $attributes=[]) {
        if ($label AND !array_key_exists("id", $attributes)) {
            $attributes["id"] = uniqid();
        }
        $attributes["name"] = $name;
        if ($label) {
            $html[] = Html::tag("label", ["for"=>$attributes["id"]], $label);
        }
        $optionsHtml = [];
        $selectedValue = array_key_exists($name, $context) ? $context[$name] : null;
        $useKeysAsValues = (!is_array($optionValues)) || (array_key_first($optionValues)!==0); // od PHP8: OR !array_is_list($optionValues);

        foreach ($optionValues as $key=>$value) {
            $optionValue = $useKeysAsValues ? $key : $value;
            $optionsHtml[] = Html::tag("option", (isset($selectedValue) AND $optionValue==$selectedValue) ? ['value'=>$optionValue , 'selected'=>true] : ['value'=>$optionValue ?? $value], $value);
        }
        $html[] = Html::tag('span', [],
                    Html::tag("select", $attributes, $optionsHtml)
                );
        return implode(PHP_EOL, $html);
    }

    /**
     * Generuje html kód input tagu typu radio. Pokud je zadán parametr label, přidá tag label svázaný s generovaným tagem input.
     *
     * Pokud je zadán parametr labelAttribites a parametr attributes neobsahuje položku "id" je jako fallback vygenerováno id jako náhodný řetězec (uniquid).
     * Pro propojení generovaného tagu label použito zadané případně vygenerované id.
     *
     * Pokud je v kontextu položka se jménem odpovídajícím parametru name pak vygenerovaný option se stejnou hodnotou jako je hodnota položky je doplněn atributem selected.
     *
     * @param type $name Jméno proměnné formuláře (má přednost před případným atributem name)
     * @param iterable $radiosetLabelsValues Asociativní pole dvojic nadpis položky => hodnota položky
     * @param array $context Kontext - asociativní pole dat získaných z formuláře.
     * @param iterable $inputAttributes atributy tagu input, případné položky "type", "name", "value" a "checked" budou přepsány automaticky generovanými hodnotami
     * @param iterable $labelAttributes atributy tygu label, případná polžka "for" bude v případě automaticky generovaného id tagu input přepsána autoticky generovanou hodnotou
     * @return string
     */
    public static function radio($name, iterable $radiosetLabelsValues=[], array $context=[], iterable $inputAttributes=[], iterable $labelAttributes=[]) {
        $checkedValue = array_key_exists($name, $context) ? $context[$name] : null;
        $inputAttributes["type"] = "radio";
        foreach ($radiosetLabelsValues as $label => $value) {
            $inputAttributes["name"] = $name;
            $inputAttributes["value"] = $value;
            $inputAttributes["checked"] = ($checkedValue===$value) ;
            if(isset($inputAttributes["id"]) AND $inputAttributes["id"]) {
                $labelAttributes["for"] = $inputAttributes["id"];
            }
            $html[] = Html::tag("label", $labelAttributes,
                    Html::tagNopair("input", $inputAttributes)
                    .
                    $label
                    );
        }
        return implode(PHP_EOL, $html);
    }

    /**
     * Generuje html kód input tagu typu chackbox.
     *
     * Pokud je zadán parametr label a parametr attributes neobsahuje položku "id" je jako fallback vygenerováno id jako náhodný řetězec (uniquid).
     * Pro propojení generovaného tagu label použito zadané případně vygenerované id.
     *
     * @param iterable $checkboxsetLabelsNameValuePairs
     * @param array $context
     * @param iterable $inputAttributes atributy tagu input, případné položky "type", "name", "value" a "checked" budou přepsány automaticky generovanými hodnotami
     * @param iterable $labelAttributes atributy tygu label, případná položka "for" bude v případě automaticky generovaného id tagu input přepsána autoticky generovanou hodnotou
     * @return string
     */
    public static function checkbox(iterable $checkboxsetLabelsNameValuePairs=[], array $context=[], iterable $inputAttributes=[], iterable $labelAttributes=[]) {
        $inputAttributes["type"] = "checkbox";
        foreach ($checkboxsetLabelsNameValuePairs as $label => $nameValuePair) {
            $name = key($nameValuePair);
            $value = $nameValuePair[$name];
            $checkedValue = array_key_exists($name, $context) ? $context[$name] : null;
            $inputAttributes["name"] = $name;
            $inputAttributes["value"] = $value;
            $inputAttributes["checked"] = ($checkedValue===$value) ;
            if(isset($inputAttributes["id"]) AND $inputAttributes["id"]) {
                $labelAttributes["for"] = $inputAttributes["id"];
            }
            $html[] = Html::tag("label", $labelAttributes,
                    Html::tagNopair("input", $inputAttributes)
                    .
                    $label
                    );
        }
        return implode(PHP_EOL, $html);
    }

}
