<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Description of DataProvider
 *
 * @author pes2704
 */
class DataProvider {

    /**
     *
     * @var \ArrayAccess
     */
    private $dataArray;


    public function __construct(\ArrayAccess $dataArray) {
        $this->dataArray = $dataArray;
    }
    public function get($id) {
        return $this->dataArray->offsetGet($id) ?? [];
    }

}
