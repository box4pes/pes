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
trait LoggingHandlerTrait {

    public function close(): bool {
        $close = parent::close();
        $this->logger->debug('Session save handler: close - success:{close}', ['close'=>$close]);
        return $close;
    }

    public function create_sid(): string {
        $sid = parent::create_sid();
        $this->logger->debug('Session save handler: create_sid - success:{sid}', ['sid'=>$sid]);
        return $sid;
    }

    public function destroy(string $id): bool {
        $destroy = parent::destroy($id);
        $this->logger->debug('Session save handler: destroy({session_id}) - success:{destroy}', ['session_id'=>$id, 'destroy'=>$destroy]);
        return $destroy;
    }

    public function gc(int $max_lifetime): int|false {
        $gc = parent::gc($max_lifetime);
        $this->logger->debug('Session save handler: gc({maxlifetime}) - success:{gc}', ['maxlifetime'=>$max_lifetime, 'gc'=>$gc]);
        return $gc;
    }

    public function open(string $path, string $name): bool {
        $open = parent::open($path, $name);
        $this->logger->debug('Session save handler: open({save_path}, {session_name}) - success:{open}', ['save_path'=>$path, 'session_name'=>$name, 'open'=>$open]);
        return $open;
    }

}
