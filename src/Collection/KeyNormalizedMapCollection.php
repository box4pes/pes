<?php
/**
 */
namespace Pes\Collection;

use Pes\Collection\Normalizer\KeyNormalizerInterface;

/**
 * NormalizedKeyMapCollection
 *
 * Kolekce párů klíč-hodnota. Rozšiřuje funkčnost MapCollection o normalizaci klíčů.
 * Pokud je nastaven normalizer, hodnoty v kolekci jsou interně ukládány podle normalizovaného klíče. Pro vytváření normalizovaé podoby klíče se používá normalizer -
 * objekt KeyNormalizatorInterface, který je nastaven metodou setKeyNormalizer().
 *
 * Možné použití je např. pro case insensitive klíče - zadaný objekt keyNormalizer musí převádět klíč na lowercase (nebo uppercase) variantu.
 */
class KeyNormalizedMapCollection extends MapCollection {

    protected $keyNormalizer;

    /**
     * Vytvoří novou kolekci
     *
     */
    public function __construct(KeyNormalizerInterface $keyNormalizer, array $array=[]) {
        $this->keyNormalizer = $keyNormalizer;
        parent::__construct($array);  // pro aray volá v cyklu set() -> použije set() KeyNormalizedMapCollection
    }

    public function setKeyNormalizer(KeyNormalizerInterface $keyNormalizer) {
        $this->keyNormalizer = $keyNormalizer;
    }

    /**
     * Vrací asociativní pole jmeno=>hodnota
     * Pole je indexováno původními nenormalizovnými jmény.
     *
     * @return array
     */
    public function getArrayCopy() {
        $out = [];
        foreach (parent::getArrayCopy() as $normalizedKey => $value) {
            $out[$this->keyNormalizer->getOriginalKey($normalizedKey)] = $value;
        }
        return $out;
    }

    /**
     * {@inheritdoc}
     *
     * Použít lze index v libovolné formě, která po normalizaci odpovídá požadovanému normalizovanému indexu.
     */
    public function set($key, $value) {
        $normalizedKey = $this->keyNormalizer->normalizeKey($key);
        parent::set($normalizedKey, $value);
    }

    /**
     * {@inheritdoc}
     *
     * Použít lze index v libovolné formě, která po normalizaci odpovídá požadovanému normalizovanému indexu.
     */
    public function get($key) {
        $normalizedKey = $this->keyNormalizer->normalizeKey($key);
        return $this->has($normalizedKey) ? parent::get($normalizedKey) : NULL;
    }

    /**
     * {@inheritdoc}
     *
     * Použít lze index v libovolné formě, která po normalizaci odpovídá požadovanému normalizovanému indexu.
     *
     */
    public function has($key) {
        return parent::has($this->keyNormalizer->normalizeKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * Použít lze index v libovolné formě, která po normalizaci odpovídá požadovanému normalizovanému indexu.
     *
     */
    public function remove($key) {
        parent::remove($this->keyNormalizer->normalizeKey($key));
    }
}
