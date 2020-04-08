<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Session\SaveHandler;

/**
 *
 * @author pes2704
 */
trait EncryptedReadWriteTrait {
    
    public function read($id) {
        $data = parent::read($id);
        if (!$data) {
            return "";
        } else {
             $ret = $this->cryptor->decrypt($data);
            return $ret;
        }
    }

    public function write($id, $data) {
        $ret = $this->cryptor->encrypt($data);
        return parent::write($id, $ret);
    }

}
