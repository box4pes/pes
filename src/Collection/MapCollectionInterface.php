<?php
namespace Pes\Collection;

/**
 *
 * @author pes2704
 */
interface MapCollectionInterface extends CollectionInterface {
    public function set($index, $value);
    public function get($index);
    public function has($index);    
    public function remove($index);
}
