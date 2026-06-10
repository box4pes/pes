<?php
    /**
     * Motivace:
     * Tabulka s kompozitním (správně kompoudním - compound) klíčem
     *
     * CREATE TABLE voting(QuestionID NUMERIC, MemberID NUMERIC);
     * nebo s primary key:
     * CREATE TABLE voting (QuestionID NUMERIC, MemberID NUMERIC, PRIMARY KEY (QuestionID, MemberID));
     *
     * můžeš volat:
     * SELECT * FROM voting WHERE QuestionID = 7 AND MemberID = 7
     * SELECT * FROM voting WHERE QuestionID = 7  (jen první část klíče - nutno použít postupně části klíče v pořadí zleva)
     * nelze volat:
     * SELECT * FROM voting WHERE MemberID = 7  (druhá část klíče v pořadí zleva)
     * a taky můžeš volat:
     * SELECT * FROM t WHERE (QuestionID, MemberID) IN ( (5,1), (7,2) );
     * pokud jsi nepoužil 'primary key', pak pro zrychlení:
     * CREATE INDEX voting_idx ON voting(QuestionID, MemberID); nebo CREATE UNIQUE INDEX voting_idx ON voting(QuestionID, MemberID);
     * a taky pro zrychlení i pro opačné pořadí zadaných klíčů CREATE UNIQUE INDEX voting_idx ON voting (MemberID, QuestionID);
     */


namespace Pes\Model\Key;

/**
 * Description of RelationKey
 *
 * @author pes2704
 */
class RelationKey implements RelationKeyInterface, \Serializable {

    private $generatedFields;
    private $attribute;
    private $keyHash;

    /**
     * Pole, které jako hodnoty má názvy polí atributu.
     * @param array $attribute
     */
    public function __construct(array $attribute, array $generatedFields=[]) {
        $this->generatedFields = $generatedFields;
        $this->attribute = $attribute;
//        $this->id = spl_object_hash($this);
//        $this->idMD5 = md5($this->id);
    }

    public function isGenerated() {
        return $this->generatedFields;
    }

    /**
     * Nastaví hodnoty klíče. Parametrem je asociativní pole, které musí mít stejné indexy jako atribut.
     * Metodu nelze použít, pokud klíč je generovaný, při pokusu o nastevení hodnot generovaného klíče metoda vyhazuje výjimku.
     * V případě generovaného klíče mohou být hodnoty klíče nastaveny pouze na hodnoty načtené z databáze.
     * Hodnoty generovaného klíče nastavuje key hydrator. Key hydrator používá reflexi a tak překoná toto omezení.
     *
     * @param array $keyHash Asociativní pole. Jednoprvkové pro simple key, n-prvkové pro compound (composite) key, Indexy musí odpovídat polím atributu.
     * @throws \UnexpectedValueException Pokud zadané pole má jiné indexy než atribut.
     */
    public function setKeyHash(array $keyHash) {
        if ($this->generatedFields) {
            foreach ($this->generatedFields as $field) {
                if (array_key_exists($field, $keyHash)) {
                    throw new \LogicException("Pole $field klíče je generované a jeho hodnotu lze nastavit pouze hydrátorem při čtení z databáze.");
                }
            }
        }
        if($this->attribute != array_keys($keyHash)) {
            throw new \UnexpectedValueException('Indexy pole hodnot neodpovídají polím atributu klíče zadaným v konstruktoru.');
        }
        $this->keyHash = $keyHash;
        return $this;
    }

    /**
     * Vrací pole názvů polí atributu
     * @return array
     */
    public function getKeyAttribute() {
        return $this->attribute;
    }

    /**
     * Vrací asociativní pole názvů/hodnot polí atributu
     * @return Array asociativní pole názvů/hodnot polí atributu
     */
    public function getKeyHash() {
        return $this->keyHash;
    }

    /**
     * Shodné klíče - mají stejné páry index/hodnota, nezáleží na pořadí.
     * @param RelationKeyInterface $key
     * @return type
     */
    public function equal(RelationKeyInterface $key) {
        //$a == $b 	Equality 	TRUE if $a and $b have the same key/value pairs. - nezáleží na pořadí - testováno
        //$a === $b 	Identity 	TRUE if $a and $b have the same key/value pairs in the same order and of the same types.)
        return $this->keyHash == $key;
    }

    /**
     * Shodný atribut klíče (jednoduchý nebo kompozitní) - klíče mají shodná pole (sloupce).
     * @param RelationKeyInterface $key
     * @return type
     */
    public function equalAttribute(RelationKeyInterface $key) {
        return array_keys($this->keyHash) == array_keys($key->getKeyHash());
    }

    /**
     * Metoda rozhraní Serializable. Serializovanou hodnotu použít nař. jako index v kolekci. Je to také příprava pro případnu serializaci entity.
     * @return string
     */
    public function serialize() {
        return serialize(
                array(
                    'attribute' => $this->attribute,
                    'generatedFields' => $this->generatedFields,
                    'keyHash' => $this->keyHash
                ));
    }

    public function unserialize($serialized) {
        $data = unserialize($serialized);
        $this->attribute = $data['attribute'];
        $this->generatedFields = $data['generatedFields'];
        $this->keyHash = $data['keyHash'];
    }
}
