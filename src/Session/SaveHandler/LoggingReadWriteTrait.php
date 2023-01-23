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
trait LoggingReadWriteTrait {

    public function read($session_id) {
        $read = parent::read($session_id);
        $this->logger->debug('Session save handler: read({session_id}) - success:{read}', ['session_id'=>$session_id, 'read'=>$read]);
        $this->logger->debug('$_SESSION: {session}', ['session'=> print_r($_SESSION, true)]);
        return $read;
    }

    public function write($session_id, $session_data) {
        $write = parent::write($session_id, $session_data);
        $this->logger->debug('$_SESSION: {session}', ['session'=> print_r($_SESSION, true)]);
        $this->logger->debug('Session save handler: write({session_id}, {session_data}) - success:{write}', ['session_id'=>$session_id, 'session_data'=>$session_data, 'write'=>$write]);
        return $write;
    }

 }
