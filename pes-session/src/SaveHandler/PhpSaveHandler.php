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

    public function close(): bool {
        $close = parent::close();
        return $close;
    }

    public function read(string $id): string|false {
        $read = parent::read($id);
        return $read;
    }

    public function write(string $id, string $data): bool {
        $write = parent::write($id, $data);
        return $write;
    }

    public function create_sid(): string {
        $sid = parent::create_sid();
        return $sid;
    }

    public function destroy(string $id): bool {
        $destroy = parent::destroy($id);
        return $destroy;
    }

    public function gc(int $max_lifetime): int|false {
        $gc = parent::gc($max_lifetime);
        return $gc;
    }

    public function open(string $path, string $name): bool {
        $open = parent::open($path, $name);
        return $open;
    }

}
