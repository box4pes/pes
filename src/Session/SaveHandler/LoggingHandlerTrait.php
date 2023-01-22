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

    public function destroy(string $session_id): bool {
        $destroy = parent::destroy($session_id);
        $this->logger->debug('Session save handler: destroy({session_id}) - success:{destroy}', ['session_id'=>$session_id, 'destroy'=>$destroy]);
        return $destroy;
    }

    public function gc(int $maxlifetime) {  // : int|false 
        $gc = parent::gc($maxlifetime);
        $this->logger->debug('Session save handler: gc({maxlifetime}) - success:{gc}', ['maxlifetime'=>$maxlifetime, 'gc'=>$gc]);
        return $gc;
    }

    public function open(string $save_path, string $session_name): bool {
        $open = parent::open($save_path, $session_name);
        $this->logger->debug('Session save handler: open({save_path}, {session_name}) - success:{open}', ['save_path'=>$save_path, 'session_name'=>$session_name, 'open'=>$open]);
        return $open;
    }

}
