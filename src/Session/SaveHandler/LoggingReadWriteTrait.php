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

    public function read(string $id): string|false {
        $read = parent::read($id);
        $this->logger->debug('Session save handler: read({id}) - success:{read}', ['id'=>$id, 'read'=>$read]);
        $this->logger->debug('After read: $_SESSION: {session}', ['session'=> print_r($_SESSION, true)]);
        return $read;
    }

    public function write(string $id, string $data): bool {
        $write = parent::write($id, $data);
        $this->logger->debug('Before wite: $_SESSION: {session}', ['session'=> print_r($_SESSION, true)]);
        $this->logger->debug('Session save handler: write({session_id}, {session_data}) - success:{write}', ['session_id'=>$id, 'session_data'=>$data, 'write'=>$write]);
        return $write;
    }

 }
