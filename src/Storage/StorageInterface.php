<?php

namespace Pes\Storage;

/**
 *
 * @author pes2704
 */
interface StorageInterface {

    public function get($key);
    public function set($key, $value);
    public function remove($key);
}
