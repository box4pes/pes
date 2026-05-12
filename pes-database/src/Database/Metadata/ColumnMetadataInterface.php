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
interface ColumnMetadataInterface {
    public function getName();
    /**
     * Přepíše výchozí hodnotu
     * @param bool $isWriteable
     */
    public function setWriteable($isWriteable):self;
    public function isWriteable(): bool;
    public function isGenerated(): bool;
    public function isPrimaryKey(): bool;
    public function getType();            
}
