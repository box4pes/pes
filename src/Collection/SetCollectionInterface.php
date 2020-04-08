<?php
namespace Pes\Collection;

/**
 *
 * @author pes2704
 */
interface SetCollectionInterface  extends CollectionInterface {
    public function set($value);
    public function has($value);
    public function remove($value);
}
