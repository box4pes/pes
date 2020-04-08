<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Cookies;

/**
 * Description of Cookie
 *
 * @author pes2704
 */
class ResponseCookie implements ResponseCookieInterface {

    private $name;
    private $value;
    private $attributes;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function setValue($value=''): ResponseCookieInterface {
        $this->value = $value ?? '';
        return $this;
    }

    /**
     * Nstavení atributů, které josu přeneseny do atributů set-cookie, které bude vloženo do response. Pokud některý atribut není nastaven, použijí se defaultní
     * hodnoty atributů objektu ResponseCookies. Obvyklým užitím tedy je spíše použití této metody pro nastavení odlišných, zvláštních atributů jednotlivých ResponseCookie
     * a použití defaultních atributů ResponseCookies objektu.
     *
     * Jména atributů set-cookies:
     * <ul>
     * <li>'Domain' String. Pokud hodnota není zadána, cookie jsou přidány je k requestu, který směřuje přesně na url skriptu, kterým byl vygenerován response (ne na subdomény).
     * Pokud je zadána, jsou cookies přidány jen k requestům směřujících na zadanou doménu a subdomény.</li>
     * <li>'HostOnly' Boolean. Pokud je hodnota zadána, cookie je odeslána jen pokud host v url přesně odpovídá zadanému Domain atributu cookie. </li>
     * <li>'Path' String. Pokud je cesta zadána, prohlížeč přidá cookies jen k requestu, kde cesta je podřetězcem url. Pokud je více cookies se
     * stejným jménem a doménou, pak by měl prohlížeč dle RFC 6265 řadit cookies tak, že přednot ma cookie s delší shodou path a url a odesílat je první cookie. Nelze na to spoléhat,
     * modrní prohlížeče takto řadí, ale nově také odesílají více cookies.</li>
     * <li>'Max-Age' Integer. Doba života cookie v sekundách.</li>
     * <li>'Expires' Instance \DateTimeInterface nebo integer nebo string. Atribut Expire udává datum a čas, kdy vyprší platnost cookie, po tomto čase prohlížeč
     * cookie dále neodesílá (a měl by ji zahodit). Nesmí být současně zadán atribut Max-Age, pokud je zadán, prohlížeč použije May-Age a atribut Expires zahodí.
     *  <ul><li>Hodnota může být zadána jako instance \DateTimeInterface, pak je z ní získán správný GMT datum a čas v závislosti na nastavené časové zóně systému. </li>
     * <li>Hodnota může být zadána jako integer, pak se chápe jako počet sekund a současnosti a datum je vypočítáno. </li>
     * <li>Hodnota múže být zadána zako řetězec a to ve formátu, který je vyhodnotitelný funkcí strtotime(). Datum a čas Expires je vytvořen právě funkcí strtotime()
     * ze zadané hodnoty atributu. Doporučuji přečíst dokumentaci, zajímavé jsou relativní formáty data. Funkce strtotime() hlásí E_NOTICE anebo E_STRICT nebo E_WARNING
     * pro něsprávně nastavenou časovou zónu. </li></ul></li>
     * <li>'Secure' Boolean. Pokud je zadána hodnota TRUE, je cookie klientem odesílána je při použití zabezpečeného (https) spojení
     * pro odeslání requestu. Nové verze prohlížečů navíc cookie s nastaveným atributem Secure zahazují už kdyř je taková cookie přijatá v responsu, který byl poskutnut nezabezpečeným kanálem
     * a takovou cookie vůbec neukládají.</li>
     * <li>'HttpOnly' Boolean. Pokud je hodnota zadána TRUE, je obsah cookie v prohlížeči dostupný jen pro vytvoření HTTP request, není dostupný pro javascript.</li>
     * <li>'SameSite' String.
     * <ul><li>Hodnota může být strict. Cookies budou odesílány pouze s requesty směřujícími na url ze stejné "site", tedy na stejné url, ze kterého byl odeslán response, který cookie nastavil. </li>
     * <li>Hodnota může být lax. Cookies nebodou odesílány s requesty automaticky vzniklými při vykreslování stránky, jako jsou načítané obrázky nebo framy,
     * ale budou odesílánz, pokud request vznikne aktivním kliknutím uživatele, například na tag a (anchor).</li></ul>
     * </li>
     *</ul>
     * @param array $attributes
     * @return \Pes\Http\Cookies\ResponseCookie
     */
    public function setAttributes(array $attributes=[]): ResponseCookieInterface {
        $this->attributes = array_change_key_case($attributes, CASE_LOWER);  // všechny metody předpokládají lowercase klíče
        return $this;
    }


}
