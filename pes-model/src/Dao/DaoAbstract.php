<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pes\Model\Dao;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Statement\StatementInterface;

use Pes\Model\Builder\SqlInterface;
use Pes\Model\RowData\RowDataInterface;
use Pes\Model\Dao\DaoContextualInterface;
use Pes\Model\Context\ContextProviderInterface;

use Pes\Model\Dao\Exception\DaoParamsBindNamesMismatchException;
use Pes\Model\Dao\Exception\DaoContextualHasNoContextFactoryException;
use UnexpectedValueException;

use PDOStatement;

/**
 * Description of DaoAbstract
 *
 * @author pes2704
 */
abstract class DaoAbstract implements DaoInterface {

    /**
     * @var HandlerInterface
     */
    protected $dbHandler;

    protected $fetchMode;

    protected $fetchClassName;
    
    /**
     * @var SqlInterface
     */
    protected $sql;

    /**
     * Prepared statements cache
     *
     * @var array
     */
    private $preparedStatements = [];

    /**
     * @var ?ContextProviderInterface
     */
    protected $contextFactory;

    public function __construct(HandlerInterface $handler, SqlInterface $sql, $fetchClassName, ?ContextProviderInterface $contextFactory=null) {
        $this->dbHandler = $handler;
        $this->fetchMode = \PDO::FETCH_CLASS;
        $this->fetchClassName = $fetchClassName;
        $this->sql = $sql;
        $this->contextFactory = $contextFactory;
    }

#### public ##########################################

    /**
     * {@inheritDoc}
     * Vrací jeméno schematu, které získá z aktuální db handleru.
     */
    public function getSchemaName() {
        return $this->dbHandler->getSchemaName();
    }
    //TODO: ? protected - getPrimaryKey je použito jen v testech - mění se hodnota primárního klíče a zpětně se získává ?? je to use case?
    /**
     * {@inheritDoc}
     * @param array $row Řádek dat - sociativní pole, které musí obsahovat alespoň položky odpovídající polím primárního klíče
     * @return array
     * @throws UnexpectedValueException
     */
    public function getPrimaryKey(array $row): array{
        $keyAttribute = $this->getPrimaryKeyAttributes();
        $key = [];
        foreach ($keyAttribute as $field) {
            if( ! array_key_exists($field, $row)) {
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Atribut klíče obsahuje pole '$field' a pole dat pro vytvoření klíče neobsahuje prvek s takovým jménem.");
            }
            if (is_scalar($row[$field])) {
                $key[$field] = $row[$field];
            } else {
                $t = gettype($row[$field]);
                throw new UnexpectedValueException("Nelze vytvořit dvojice jméno/hodnota pro klíč entity. Zadaný atribut klíče obsahuje v položce '$field' neskalární hodnotu. Hodnoty v položce '$field' je typu '$t'.");
            }
        }
        return $key;
    }

    /**
     * {@inheritDoc}
     *
     * @param array $primaryKey
     * @return RowDataInterface|null
     */
    public function get(array $primaryKey): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                $this->getConditionsFromContext(),
                $this->sql->touples($this->getPrimaryKeyAttributes())
            )
        );
        $touplesToBind = $this->getPrimaryKeyPlaceholdersValues($primaryKey);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $uniqueKey
     * @return RowDataInterface|null
     */
    public function getUnique(array $uniqueKey): ?RowDataInterface {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and(
                $this->getConditionsFromContext(),
                $this->sql->touples(array_keys($uniqueKey))
            )
        );
        $touplesToBind = $this->getPlaceholdersValues($uniqueKey);
        return $this->selectOne($select, $from, $where, $touplesToBind, true);
    }

    public function findNonUnique(array $nonUniqueKey): iterable {
        $whereClause = $this->sql->and(
                $this->getConditionsFromContext(),
                $this->sql->touples(array_keys($nonUniqueKey))
            );
        return $this->find($whereClause, $nonUniqueKey);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $whereClause Příkaz where v SQL syntaxi vhodné pro PDO, s placeholdery
     * @param array $touplesToBind Pole dvojic jméno-hodnota, ze kterého budou budou nahrazeny placeholdery v příkazu where
     * @return iterable<RowDataInterface>
     */
    public function find($whereClause="", array $touplesToBind=[]): iterable {
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($whereClause);
        return $this->selectMany($select, $from, $where, $touplesToBind);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable<RowDataInterface>
     */
    public function findAll(): iterable{
        $select = $this->sql->select($this->getAttributes());
        $from = $this->sql->from($this->getTableName());
        $where = $this->sql->where($this->sql->and($this->getConditionsFromContext()));
        return $this->selectMany($select, $from, $where, []);
    }

    private function getConditionsFromContext() {
        if ($this instanceof DaoContextualInterface) {
            if (isset($this->contextFactory)) {
                $ret = $this->getContextConditions();   // metoda DaoContextualInterface - musí ji implementovat konkrétní DAO
            } else {
                throw new DaoContextualHasNoContextFactoryException("DAO typu DaoContextualInterface musí mít v konstruktoru předán objekt ContextFactoryInterface.");
            }
        } else {
            $ret = [];
        }
        return $ret;
    }


#################################################################################

    /**
     * Očekává SQL string s příkazem SELECT a případnými placeholdery. Provede ho s použitím parametrů a vrací jednu řádku tabulky ve formě dané nastavením parametrů konstruktoru.
     * V případě úspěchu vrací objekt nebo asociativní pole, v případě neúspěchu vrací false (viz metoda PDOStatement fetch()).
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací jednu řádku tabulky ve formě objektu nebo asociativního pole. Pokud provedení příkazu vede k vyhledání více než jedné
     * řádky, vrací jen první nalezenou a pokud je nastaven parametr $checkDuplicities na TRUE, pak v takovém případě vznikne user error typu E_USER_WARNING
     * s hlášením o duplicitním záznamu. I v případě duplicitního záznamu vrací první vyhledaný řádek, nevyhazuje výjimku.
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @param bool $checkDuplicities Nepovinný parametr, default FALSE.
     * @return RowDataInterface|null
     */
    protected function selectOne($select, $from, $where, $touplesToBind=[], $checkDuplicities=FALSE): ?RowDataInterface {
        $statement = $this->getPreparedStatement($select.$from.$where);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $statement->execute();
        if ($checkDuplicities) {
            $num_rows = $statement->rowCount();
            if ($num_rows > 1) {
                user_error("V databázi existuje duplicitní záznam.". "Dao: ".get_called_class().", ". print_r($touplesToBind, true), E_USER_WARNING);
            }
        }
        $rowData = $statement->fetch();  // vrací rowDta nebo false
        return $rowData ? $rowData : null; // vrací rowData nebo null
    }

    /**
     * Očekává SQL string s příkazem SELECT a případnými placeholdery. Provede ho s použitím parametrů a vrací vyhledané řádky ve formě asociativního pole.
     *
     * Podrobně:
     * - Vyzvedne vytvořený prepared statement pro zadaný SQL řetězec z cache, pokud není vytvoří nový prepared statement a uloží do cache.
     * - Nahradí placeholdery zadanými parametry pomocí bindParams().
     * - Provede příkaz a vrací vyhledané řádky ve formě asociativního pole.
     *
     * @param string $sql SQL příkaz s případnými placeholdery.
     * @param array $touplesToBind Pole parametrů pro bind, nepovinný parametr, default prázdné pole.
     * @return array
     */
    protected function selectMany($select, $from, $where='', $touplesToBind=[]) {
        $statement = $this->getPreparedStatement($select.$from.$where);
        if ($touplesToBind) {
            $this->bindParams($statement, $touplesToBind);
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Vrací prepared statement z cache. Pokud není v cache, vytvoří nový prepared statement, uliží do cache a vrací.
     *
     * @param type $sql
     * @return StatementInterface
     */
    protected function getPreparedStatement($sql): StatementInterface {
        if (!isset($this->preparedStatements[$sql])) {
            $statement =$this->dbHandler->prepare($sql);
            $statement->setFetchMode($this->fetchMode, $this->fetchClassName);            //(...$this->fetchMode);
            $this->preparedStatements[$sql] = $statement;
        }
        return $this->preparedStatements[$sql];
    }

    /**
     * Z pole hodnot vytvoří asociativní pole hodnot indexovaných pomocí placeholderů.
     *
     * @param array $values
     * @return array
     * @throws UnexpectedValueException
     */
    protected function getPrimaryKeyPlaceholdersValues(array $values) {
        $touples = [];
        foreach ($this->getPrimaryKeyAttributes() as $field) {
            if (!array_key_exists($field, $values)) {
                throw new UnexpectedValueException("v předaném klíči není prvek pro pole primárního klíče '$field'.");
            }
            if(!is_scalar($values[$field])) {
                $type = gettype($values[$field]);
                throw new UnexpectedValueException("Prvky pole primárního klíče musí mít skalární hodnoty. v předaném klíči je prvek pro pole primárního klíče '$field' který nemá skalární hodnotu, je typu '$type'.");
            }
            $touples[":".$field] = $values[$field];
        }
        return $touples;
    }

    protected function getPlaceholdersValues(array $attributePairs) {
        $touples = [];
        $attributes = $this->getAttributes();
        foreach ($attributePairs as $field=>$value) {
            if (!in_array($field, $attributes)) {
                throw new DaoParamsBindNamesMismatchException("v předaném poli dvojic je prvek '$field', který nemá odpovídající atribut (sloupec tabulky).");
            }
            $touples[":".$field] = $value;
        }
        return $touples;
    }

    /**
     *
     * Filtr obsahuje výčet jmen, která se mají skutečně použít z dvojic jméno-hodnota ($touplesToBind). Pokud filtr není zadán použijí se všechny hodnoty. Použití filtru je nutné,
     * pokud SQL příkaz neobsahuje všechny jména vyskytující se v dvojicí jméno-hodnota toupleToBind. Typicky situace kdy data jsou získána extrakcí entity a príkaz je DELETE,
     * který obsahje pouze položky polí klíče.
     *
     * Použití prefixu pro placeholdery umožňuje volat tuto metodu opakovaně s různým prefixem (resp. jednou s prefixema podruhé bez prefixu) a tak provést bind
     * parametru s tímtéž jménem dvakrát. Využito je typicky při volání SQK update, kde sloupec, který je součástí klíče (a tedy hodnota nahrazuje placeholder v klausuli WHERE)
     * je také updatován (a nová hodnota nahrazuje placeholder v klasuli SET).
     *
     * @param PDOStatement $statement
     * @param iterable $touplesToBind Pole nebo ArrayAccess obsahující dvojice jméno-hodnota
     * @param iterable $filterNames Filtr - výčet jmen.
     * @param type $placeholderPrefix Pokud je hodnota zadána, použije jako prefix před jménem parametru
     * @return PDOStatement
     */
    protected function bindParams(PDOStatement $statement, iterable $touplesToBind, iterable $filterNames=[], $placeholderPrefix='') {
        if($filterNames) {
            foreach ($filterNames as $name) {
                $this->getValueAndBind($statement, $touplesToBind, $name, $placeholderPrefix);
            }
        } else {
            foreach (array_keys($touplesToBind) as $name) {
                $this->getValueAndBind($statement, $touplesToBind, $name, $placeholderPrefix);
            }
        }

        return $statement;
    }
    
    private function getValueAndBind($statement, $touplesToBind, $name, $placeholderPrefix): void {
        $value = $this->getValue($touplesToBind, $name);
        if (isset($value)) {
            $param = false;
            if(is_string($value)) {
                $param = \PDO::PARAM_STR;
            } elseif(is_bool($value)) {
                $param = \PDO::PARAM_BOOL;
            } elseif(is_int($value)) {
                $param = \PDO::PARAM_INT;
            }
            if ($param) {
                $statement->bindValue($placeholderPrefix.$name, $value, $param);
            } else {
                $statement->bindValue($placeholderPrefix.$name, $value);                
            }
        } else {
            $statement->bindValue($placeholderPrefix.$name, null, \PDO::PARAM_NULL);
        }        
    }

    private function getValue($touplesToBind, $name) {
        // nelze použít isset($touplesToBind[$name]) - vrací true i pro hodnoty null
        if (is_array($touplesToBind)) {
            if (array_key_exists($name, $touplesToBind)) {
                $val = $touplesToBind[$name];
            } else {
                throw new DaoParamsBindNamesMismatchException("Pole obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu neobsahuje položku se jménem '$name'.");
}
        } elseif($touplesToBind instanceof \ArrayAccess) {
            if ($touplesToBind->offsetExists($name)) {
                $val = $touplesToBind->offsetGet($name);
            } else {
                throw new DaoParamsBindNamesMismatchException("Objekt obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu neobsahuje položku se jménem '$name'.");
            }
        } else {
            $t = gettype($touplesToBind);
            throw new DaoParamsBindNamesMismatchException("Proměnná obsahující dvojice jméno/hodnota pro bind parametrů sql příkazu není typu array ani ArrayAccess. Je typu '$t'.");
        }
        return $val;
    }

}
