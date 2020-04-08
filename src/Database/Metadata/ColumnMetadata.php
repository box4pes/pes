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
 * Description of ColumnMetadata
 *
 * @author pes2704
 */
class ColumnMetadata implements ColumnMetadataInterface {
    // Extra, Field, Key, Null, Type, s parametrem FULL pak ještě Collation, Privileges, Comment

    private $Field;
    private $Default;
    private $Key;
    private $Null;
    private $Extra;
    private $Type;

    private $isWriteable = TRUE;
    private $isPrimaryKey = FALSE;
    private $isGenerated = FALSE;
    private $contentType;

    public function __construct() {
        $this->evaluateKey();
        $this->evaluateExtra();
        $this->evaluateType();
    }

    public function getName() {
        return $this->Field;
    }

    /**
     * Přepíše výchozí hodnotu
     * @param bool $isWriteable
     */
    public function setWriteable($isWriteable): ColumnMetadataInterface {
        $this->isWriteable = $isWriteable;
    }

    public function isWriteable(): bool {
        return $this->isWriteable;
    }

    public function isGenerated(): bool {
        return $this->isGenerated;
    }

    public function isPrimaryKey(): bool {
        return $this->isPrimaryKey;
    }

    public function getType() {
        return $this->contentType;
    }

    private function evaluateType() {
        $leftBracket = strpos($this->Type, '(');
        if ($leftBracket) {
            $this->contentType = substr($this->Type, 0, $leftBracket);
        } else {
              $space = strpos($this->Type, ' ');
            if ($space) {
                $this->contentType = substr($this->Type, 0, $space);
            } else {
                $this->contentType = $this->Type;
            }
        }
    }

    private function evaluateKey() {

//    If Key is empty, the column either is not indexed or is indexed only as a secondary column in a multiple-column, nonunique index.
//    If Key is PRI, the column is a PRIMARY KEY or is one of the columns in a multiple-column PRIMARY KEY.
//    If Key is UNI, the column is the first column of a UNIQUE index. (A UNIQUE index permits multiple NULL values, but you can tell whether the column permits NULL by checking the Null field.)
//    If Key is MUL, the column is the first column of a nonunique index in which multiple occurrences of a given value are permitted within the column.

        if (strpos($this->Key, 'PRI') !== FALSE) {
            $this->isPrimaryKey = TRUE;
        }
    }

    private function evaluateExtra() {
//    The value is nonempty in these cases:
//    - auto_increment for columns that have the AUTO_INCREMENT attribute
//    - on update CURRENT_TIMESTAMP for TIMESTAMP or DATETIME columns that have the ON UPDATE CURRENT_TIMESTAMP attribute
//    - VIRTUAL GENERATED or VIRTUAL STORED for generated columns


        if (strpos($this->Extra, 'auto_increment') !== FALSE) {
            $this->isWriteable = FALSE;
            $this->isGenerated = TRUE;
        }
        if (strpos($this->Extra, 'on update CURRENT_TIMESTAMP') !== FALSE) {
            $this->isWriteable = FALSE;
            $this->isGenerated = TRUE;
        }


    }

}
