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

use Pes\Utils\Directory;

/**
 * Description of SaveHandler
 * Třída je session save handler a implementuje metody PHP SessionHandlerInterface. Nepoužívá volání PHP funkcí pro práci se session,
 * nepoužívá zabudovaný mechanismus PHP pro práci se session daty.
 *
 * Je převzata z příkladu v dokumentaci PHP http://php.net/manual/en/class.sessionhandlerinterface.php
 *
 * @author pes2704
 */
class FileSaveHandler implements \SessionHandlerInterface
{
    private $savePath;


    public function open(string $path, string $name): bool {
        $this->savePath = Directory::createDirectory($path);
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read(string $id): string|false {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write(string $id, string $data): bool {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy(string $id): bool {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc(int $max_lifetime): int|false {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $max_lifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}
