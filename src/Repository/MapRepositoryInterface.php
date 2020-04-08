<?php
/**
 * 
 * @author pes2704
 */
namespace Pes\Repository;

interface MapRepositoryInterface
{
    function get($index);
    function set($index, $value);
    function remove($index);
}

