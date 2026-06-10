<?php

namespace Pes\Model\RowData\Filter;

/**
 *
 * @author pes2704
 */
interface NominateFilterInterface {

    /**
     * Přijímá pole jmen klíčů položek akceptovaných iterátorem.
     * 
     * @param array $keys
     * @return void
     */
    public function nominate(array $keys=[]): void;

    /**
     * Vrací info, zda je aktuální položka, ke které dospěl iterátor akceptovatelná filtrem.
     *
     * @return bool
     */
    public function accept(): bool;
}
