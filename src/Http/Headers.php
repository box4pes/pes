<?php
/**
 */
namespace Pes\Http;

use Pes\Collection\KeyNormalizedMapCollection;
use Pes\Http\HeadersInterface;

/**
 * Headers
 *
 * Kolekce HTTP hlaviček.
 * Je použita v objektech HTTP request a response.
 * Umožňuje zadávat jména hlaviček jako case insensitive, přesněji v libovolném tvaru, který vede ke stejnému výsledku po normalizaci jména.
 * Třída používá jako interní úložiště kolekci Pes\Collection\NormalizedKeyMapCollection a jako normalizátor jmen této kolekci nastavuje objekt HeaderKeyNormalizer.
 * Pro přesnější informaci o narmalizaci viz HeaderKeyNormalizer.
 *
 * Každá HTTP hlavička muže obsahovat více skalárních hodnot současně.
 * Tato třída ukládá hodnoty do pole pro každou hlavičku. Metody, které vracejí hodnoty hlaviček, vracejí
 * pole hodnot každé hlavičky (i v případě jedné hodnoty).
 */
class Headers extends KeyNormalizedMapCollection implements HeadersInterface {

    /**
     * Konstruktor. Nastaví jako interní úložiště kolekci Pes\Collection\NormalizedKeyMapCollection a jako normalizátor jmen HeaderKeyNormalizer.
     * Hlavičky jsou ukládány pod jmény v normalizovaném tvaru.
     *
     * Obvykle je volán s polem dat získaných z objektu Environment - viz Pes\Http\Factory\HeadersFactory.
     *
     * @param array $data Pole HTTP hlaviček
     */
    public function __construct(array $data=[]) {
        parent::__construct(new HeaderKeyNormalizer(), $data);
    }

    private function convertToArray($values) {
        if (!is_array($values)) {
            $values = [$values];
        }
        return $values;
    }

    /**
     * Přidá další
     * @param type $key
     * @param type $value
     */
    public function appendValue($key, $value) {
        $oldValues = $this->get($key);
        $newValues = $this->convertToArray($value);
        $this->set($key, array_merge($oldValues, array_values($newValues)));
    }

    /**
     * Vrací asociativní pole jmeno=>hodnota HTTP hlaviček.
     * Pole je indexováno původními nenormalizovanými jmény hlaviček zadanými v metodě set(). Předpokládá se, že aplikace, která hlavičky vložila
     * s nějakým jménem se je pokuší číst se stejným jménem.
     *
     * @return array
     */
    public function all() {
        return $this->getArrayCopy();
    }

    /**
     * Nastaví hodnotu HTTP hlavičky.
     *
     * Hodnota HTTP hlavičky jsou vždy ukládány jako pole. Metoda přijímá jako hodnotu hlavičky pole i jednoduchou hodnotu, ale v tom případě
     * hodnotu převede na jednoprvkové pole. Metoda přepíše dříve zadané hodnoty hlavičky s ekvivalentním jménem. Ekvivalentní jména
     * jsou jména se stejným normalizovaným tvarem. Normalizace je prováděna objektem HeaderKeyNormalizer.
     * Hlavičky v kolekci jsou vyhledávány podle normalizovaného tvaru jména.
     *
     * @param string $key Jméno hlavičky
     * @param string $values Hodnoty hLavičky
     */
    public function set($key, $values) {
        return parent::set($key, $this->convertToArray($values));
    }

    /**
     * Vrací hodnoty HTTP hlavičky jako pole. Pokud hlavička neexistuje, vrací prázdné pole.
     * Hlavičky v kolekci jsou vyhledávány podle normalizovaného tvaru jména.
     *
     * @param  string  $key Jméno hlavičky
     *
     * @return array
     */
    public function get($key) {
        return parent::has($key) ? parent::get($key) : [];
    }
}
