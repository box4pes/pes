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

use Pes\Security\Cryptor\CryptorInterface;

/**
 * Description of FileEncryptedSaveHandler
 *
 * @author pes2704
 */
class FileEncryptedSaveHandler extends FileSaveHandler {

    /**
     * @var CryptorInterface
     */
    private $cryptor;

    public function __construct(CryptorInterface $cryptor) {
        $this->cryptor = $cryptor;
    }

    use EncryptedReadWriteTrait;

}
