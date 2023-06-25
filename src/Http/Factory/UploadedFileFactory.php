<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

use Pes\Http\UploadedFile;

/**
 * Description of UploadedFileFactory
 *
 * @author pes2704
 */
class UploadedFileFactory implements UploadedFileFactoryInterface {
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile(
                    $stream,
                    $size,
                    $error,
                    $clientFilename,
                    $clientMediaType
                );
    }

}
