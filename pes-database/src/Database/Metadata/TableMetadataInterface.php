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
 *
 * @author pes2704
 */
interface TableMetadataInterface extends \IteratorAggregate {
    public function getTableName();
    public function getColumnsNames();
    public function getColumnMetadata($name): ?ColumnMetadataInterface;
    public function getColumns();
    public function getPrimaryKeyAttribute();
    public function isInPrimaryKey($name);
}
