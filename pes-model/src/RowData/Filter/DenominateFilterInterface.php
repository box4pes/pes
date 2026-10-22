<?php

namespace Pes\Model\RowData\Filter;

/**
 *
 * @author pes2704
 */
interface DenominateFilterInterface {

    /**
     * Přijímá pole jmen klíčů položek, které nebudou akceptovány iterátorem. Ostatní položky, tedy položky nevyjmenované budou všechny akceptovány.
     * pokud metoda nebude vůběc volána nebo bude předáno prázdné pole, filtr bude při iteraci akceptovat všechny položky.
     *
     * @param array $names
     * @return void
     */
    public function denominate(array $names=[]): void;

    /**
     * Vrací info, zda je aktuální položka, ke které dospěl iterátor akceptovatelná filtrem.
     * 
     * @return bool
     */
    public function accept(): bool;
}
