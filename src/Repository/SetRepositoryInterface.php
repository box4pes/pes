<?php
/**
 *
 * @author pes2704
 */
namespace Pes\Repository;

interface SetRepositoryInterface extends \Countable, \IteratorAggregate
{
    //entita se mění a přitom zůstáva členem repository - o případném uložení do úložiště rozhoduje mapper
    
    function set($value);
    function remove($value);
}