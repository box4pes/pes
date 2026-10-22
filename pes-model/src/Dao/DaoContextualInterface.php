<?php
namespace Pes\Model\Dao;

use Pes\Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoContextualInterface {

    /**
     * Vrací podmínky v závislosti na kontextu
     * Musí vracet pole řetězců - podmínek, které budou spojeny operátorem AND do klasule WHERE v metodách DAO pro čtení z databáze.
     * @return array
     */
    public function getContextConditions(): array ;

}
