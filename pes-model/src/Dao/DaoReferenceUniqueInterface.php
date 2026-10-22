<?php
namespace Pes\Model\Dao;

use Pes\Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
interface DaoReferenceUniqueInterface extends DaoWithReferenceInterface {

    public function getByReference($referenceName, array $key): ?RowDataInterface;

}
