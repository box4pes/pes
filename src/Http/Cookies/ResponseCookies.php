<?php
/**
 * upraveno z:
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2016 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Pes\Http\Cookies;

use Psr\Http\Message\ResponseInterface;

/**
 * ResponseCookies
 */
class ResponseCookies implements ResponseCookiesInterface {

    /**
     * Cookies for HTTP response
     *
     * @var array
     */
    protected $responseCookies = [];

    /**
     * Default cookie attributes
     *
     * @var array
     */
    protected $defaults = [
        'domain' => null,
        'hostonly' => null,
        'path' => null,
        'max-age' => null,
        'expires' => null,
        'secure' => false,
        'httponly' => false,
        'samesite' => null
    ];

    private $genericNames = [
        'domain' => 'Domain',
        'hostonly' => 'HostOnly',
        'path' => 'Path',
        'max-age' => 'Max-Age',
        'expires' => 'Expires',
        'secure' => 'Secure',
        'httponly' => 'HttpOnly',
        'samesite' => 'SameSite'
    ];

    /**
     * Nastaví výchozí, defaultní atributy, se kterými budou všechny set-cookies vloženy do response. Tyto atributy budou přepsány hodnotami nastavenými jednotlivým
     * objektům ResponseCookie. Pokud tedy objekt ResponseCookie nemá nastaven některý z atributů, bude použita zde nastavená default hodnota.
     *
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
     * @param array $settings
     * @return $this
     */
    public function setDefaults(array $settings)
    {
        // všechny metody předpokládají lowercase klíče
        $this->defaults = array_replace($this->defaults, array_change_key_case($settings, CASE_LOWER));
        return $this;
    }

    /**
     * Přidá response cookie. Přidání cookie se se jménem přepíše dříve uloženou cookie.
     *
     * @param \Pes\Http\Cookies\ResponseCookieInterface $cookie
     * @return $this
     */
    public function setResponseCookie(ResponseCookieInterface $cookie) {
        $this->responseCookies[$cookie->getName()] = $cookie;
        return $this;
    }

    /**
     * Vrací objekt Response s hlavičkou SetCookie, které jako hodnotu nastaví pole hodnot včech Set-cookie oddělená čárkou. Viz RFC 6265.
     *
     * @return ResponseInterface
     */
    public function hydrateResponseRHeaders(ResponseInterface $response) {
        foreach ($this->responseCookies as $responseCookie) {
            $response = $response->withAddedHeader('Set-Cookie',  $this->createSetCookieHeaderValue($responseCookie));
        }
        return $response;
    }

    /**
     * Convert to string suitable as value for `Set-Cookie` header
     *
     * @param ResponseCookieInterface $cookie
     * @return string
     */
    private function createSetCookieHeaderValue(ResponseCookieInterface $cookie) {

        $result = \urlencode($cookie->getName()).'='.\urlencode($cookie->getValue());
        $attributes = array_replace($this->defaults, array_change_key_case($cookie->getAttributes(), CASE_LOWER));  // všechny metody předpokládají lowercase klíče

        $result .= $this->appendStringAtribute('domain', $attributes);
        $result .= $this->appendStringAtribute('path', $attributes);
        $result .= $this->appendIntegerAtribute('max-age', $attributes);
        $result .= $this->appendExpiresAttribute($attributes);
        $result .= $this->appendBooleanAttribute('secure', $attributes);
        $result .= $this->appendBooleanAttribute('hostonly', $attributes);
        $result .= $this->appendBooleanAttribute('httponly', $attributes);
        $result .= $this->appendSameSiteAttribute($attributes);

        return $result;
    }

    private function appendBooleanAttribute($attributeName, $attribUtes) {
        if (isset($attribUtes[$attributeName]) AND $attribUtes[$attributeName]) {
            return "; ".$this->genericNames[$attributeName];
        }
    }

    private function appendStringAtribute($attributeName, $attribUtes) {
        if (isset($attribUtes[$attributeName])) {
            return "; ".$this->genericNames[$attributeName]."=" . (string) $attribUtes[$attributeName];
        }
    }

    private function appendIntegerAtribute($attributeName, $attribUtes) {
        if (isset($attribUtes[$attributeName])) {
            return "; ".$this->genericNames[$attributeName]."=" . (int) $attribUtes[$attributeName];
        }
    }

    /**
     *
     * @param type $attributes
     * @return string
     * @throws \InvalidArgumentException
     */
    private function appendExpiresAttribute($attributes) {
        if (isset($attributes['expires'])) {
            if (isset($attributes['max-age'])) {
                user_error("Chyba při nastavování cookie atributu Expires. Cookie {$attributes['name']} má nastaven atribut Max-Age. Nastavený atribut Expires bude klientem zahozen.", E_USER_NOTICE);
            }
            $expires = $attributes['expires'];
            if ($expires instanceof \DateTimeInterface) {
                $seconds = $expires->getTimestamp();
            } elseif (is_numeric($expires)) {
                $seconds = (int) $expires;
            } else {
                $seconds = strtotime($expires);
            }
            if (! is_int($seconds)) {
                throw new \InvalidArgumentException("Cookie \"{name}\" has invalid expires attribute. Expirest cookie attribute must be int|\DateTimeInterface|string|null type cnvertible into time in seconds, provided type {type}.", ['type'=> gettype(expires)]);
            }
            return "; ".$this->genericNames['expires']."=" . gmdate('D, d-M-Y H:i:s T', $seconds); // Slim: gmdate('D, d-M-Y H:i:s e', $seconds)
        }
    }


    private function appendSameSiteAttribute($attributes) {
        if (isset($attributes['samesite'])) {
            $sameSiteType = new SameSiteEnum();
            return "; ".$this->genericNames['samesite']."=" . $sameSiteType($attributes['samesite']);
        }
    }
}
