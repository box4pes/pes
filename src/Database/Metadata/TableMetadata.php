<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Database\Metadata;

/**
 * Description of TableMetadata
 *
 * @author pes2704
 */
class TableMetadata implements TableMetadataInterface {

    private $tableName;
    private $columns = [];
    private $primaryKeyAttributes=[];

    public function __construct($tableName) {
        $this->tableName = $tableName;
    }

    public function setColumnMetadata(ColumnMetadataInterface $columnMetadata) {
        $this->columns[$columnMetadata->getName()] = $columnMetadata;
        if($columnMetadata->isPrimaryKey()) {
            $this->primaryKeyAttributes[] = $columnMetadata->getName();
        }
    }

    public function getTableName() {
        return $this->tableName;
    }
    
    public function getColumnsNames() {
        return array_keys($this->columns);
    }

    public function getColumnMetadata($name): ?ColumnMetadataInterface {
        return array_key_exists($name, $this->columns) ? $this->columns[$name] : NULL;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getIterator() {
        return new \ArrayIterator($this->columns);
    }

    /**
     *
     * @return array
     */
    public function getPrimaryKeyAttribute() {
        return $this->primaryKeyAttributes;
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function isInPrimaryKey($name) {
        return in_array($name, $this->primaryKeyAttributes);
    }
}
