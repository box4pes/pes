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
 * Description of PhpSaveHandler
 *
 * @author pes2704
 */
class PhpSaveHandler extends \SessionHandler implements \SessionHandlerInterface {

    public function close() {
        $close = parent::close();
        return $close;
    }

    public function read($id) {
        $read = parent::read($id);
        return $read;
    }

    public function write($id, $data) {
        $write = parent::write($id, $data);
        return $write;
    }

    public function create_sid() {
        $sid = parent::create_sid();
        return $sid;
    }

    public function destroy($session_id) {
        $destroy = parent::destroy($session_id);
        return $destroy;
    }

    public function gc($maxlifetime) {
        $gc = parent::gc($maxlifetime);
        return $gc;
    }

    public function open($save_path, $session_name) {
        $open = parent::open($save_path, $session_name);
        return $open;
    }

}
