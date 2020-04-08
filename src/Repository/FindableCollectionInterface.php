<?php
namespace Pes\Repository;
/**
 *
 * @author pes2704
 */
interface FindableRepositoryInterface {
    function find(CriteriaInterface $criteria);
}
