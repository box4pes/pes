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


    public function open($savePath, $sessionName)
    {
        $this->savePath = Directory::createDirectory($savePath);
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}
