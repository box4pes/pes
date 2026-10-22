<?php

namespace Pes\Model\Key;

/**
 *
 * @author pes2704
 */
interface RelationKeyInterface {

    public function isGenerated();
    public function getKeyAttribute();
    public function getKeyHash();
    public function setKeyHash(array $keyHash);

    /**
     * Shodné klíče - mají stejné páry index/hodnota, nezáleží na pořadí.
     */
    public function equal(RelationKeyInterface $key);

    /**
     * Shodný atribut klíče (jednoduchý nebo kompozitní) - klíče mají shodná pole (názvy sloupců).
     */
    public function equalAttribute(RelationKeyInterface $key);
}
