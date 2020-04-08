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
trait EncryptedLoggingReadWriteTrait {

    public function read($session_id) {
        $readed = parent::read($session_id);
        $this->logger->debug('Session save handler: read({session_id}) - readed {readed}', ['session_id'=>$session_id, 'readed'=>$readed]);
        if (!$readed) {
            return "";
        } else {
            $decrypted = $this->cryptor->decrypt($readed);
            if (PES_DEVELOPMENT) {
                $this->logger->debug('Session save handler: decrypted {decrypted}', ['decrypted'=>$decrypted]);
            }
            return $decrypted;
        }
    }

    public function write($session_id, $data) {
        if (PES_DEVELOPMENT) {
            $this->logger->debug('Session save handler: data to write {data}', ['data'=>$data]);
        }
        $encrypted = $this->cryptor->encrypt($data);
        $write = parent::write($session_id, $encrypted);
        $this->logger->debug('Session save handler: write({session_id}, {session_data}) - success:{write}', ['session_id'=>$session_id, 'session_data'=>$encrypted, 'write'=>$write]);
        return $write;
    }

}
