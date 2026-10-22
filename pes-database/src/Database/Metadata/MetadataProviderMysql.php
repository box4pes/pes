<?php
/**
 * Description of MysqlMetadata
 *
 * @author pes2704
 */
namespace Pes\Database\Metadata;

use Pes\Database\Metadata\TableMetadata;
use Pes\Database\Metadata\ColumnMetadata as ColumnMetadata;


class MetadataProviderMysql implements MetadataProviderInterface {

    private $dbh;
    private $attributes = [];

    public function __construct(\PDO $handler) {
        $this->dbh = $handler;
     }

    /**
     * Vrací pole atributů (názvů sloupců) a default hodnot tabulky.
     * @param type $tableName Název tabulky
     * @return array Asociativní pole sloupců tabulky, klíče prvků pole jsou názvy sloupců, hodnoty jsou default hodnoty slouopců tabulky.
     */
    public function getTableMetadata($tableName): TableMetadataInterface {
        if (!isset($this->attributes[$tableName])) {
            $this->readColumns($tableName);
        }
        return $this->attributes[$tableName];
    }

    public function getAllTablesMetadata() {
        $query = "SHOW TABLES";
        $statement = $this->dbh->prepare($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        if ($statement->execute()) {
            foreach ($statement->fetchAll() as $tableName) {
                $this->readColumns($tableName);
            }
        }
        return $this->attributes;
    }

    /**
     * Vrací název sloupce s primárnám klíčem tabulky
     * @param type $tableName
     * @return type
     */
//    public function getPrimaryKeyName($tableName) {
//        if (!isset($this->attributes[$tableName])) {  //tabulka nemusí mít primární klíč, ale vždy má sloupce
//            $this->readColumns($tableName);
//        }
//        return $this->primaryKeys[$tableName];
//    }

    private function readColumns($tableName) {
        //Nacteni struktury tabulky, datovych typu a ost parametru tabulky
        $tableMetadata = new TableMetadata($tableName);
        $query = "SHOW COLUMNS FROM ".$tableName;
        $statement = $this->dbh->prepare($query);
        if ($statement->execute()) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, ColumnMetadata::class);   // bez FETCH_PROPS_LATE nejdříve nastaví properties, pak volá konstruktor
            foreach($statement->fetchAll() as $columnMetadata) {
                // Extra, Field, Key, Null, Type, s parametrem FULL pak ještě Collation, Privileges, Comment
                $tableMetadata->setColumnMetadata($columnMetadata);
            }
            $this->attributes[$tableName] = $tableMetadata;
        }
    }


//    https://dev.mysql.com/doc/refman/5.7/en/columns-table.html
// The following statements are nearly equivalent:
//
//SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
//  FROM INFORMATION_SCHEMA.COLUMNS
//  WHERE table_name = 'tbl_name'
//  [AND table_schema = 'db_name']
//  [AND column_name LIKE 'wild']
//
//SHOW COLUMNS
//  FROM tbl_name
//  [FROM db_name]
//  [LIKE 'wild']

}
