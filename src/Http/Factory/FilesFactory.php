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
use Pes\Http\Stream;
use Pes\Http\Environment;
use Pes\Http\UploadedFile;

/**
 * Description of UploadedFileFactory
 *
 * @author pes2704
 */
class FilesFactory implements FilesFactoryInterface {

    private $uploadedFilesFactory;

//    public function createUploadedFile(
//        StreamInterface $stream,
//        int $size = null,
//        int $error = \UPLOAD_ERR_OK,
//        string $clientFilename = null,
//        string $clientMediaType = null
//    ): UploadedFileInterface;

    public function __construct(UploadedFileFactoryInterface $uploadedFilesFactory) {
        $this->uploadedFilesFactory = $uploadedFilesFactory;
    }

    /**
     * Create a normalized tree of UploadedFile instances from the Environment.
     *
     * @param Environment $env The environment
     *
     * @return array|null A normalized tree of UploadedFile instances or null if none are provided.
     */
    public function createFiles()
    {
        if (isset($_FILES)) {
            return static::parseUploadedFiles($_FILES);
        }
        return [];
    }
    
    /**
     * Parse a non-normalized, i.e. $_FILES superglobal, tree of uploaded file data.
     *
     * @param array $uploadedFiles The non-normalized tree of uploaded file data.
     *
     * @return array A normalized tree of UploadedFile instances.
     */
    private function parseUploadedFiles(array $uploadedFiles)
    {
        $parsed = [];
        foreach ($uploadedFiles as $field => $uploadedFile) {
            if (!isset($uploadedFile['error'])) {
                if (is_array($uploadedFile)) {
                    $parsed[$field] = static::parseUploadedFiles($uploadedFile);
                }
                continue;
            }

            $parsed[$field] = [];
            if (!is_array($uploadedFile['error'])) {
                $parsed[$field] = 
                $this->createUploadedFile(
//                        new UploadedFile(
                    $uploadedFile['tmp_name'],              // předávám filename
                    isset($uploadedFile['size']) ? $uploadedFile['size'] : null,
                        $uploadedFile['error'],
                    isset($uploadedFile['name']) ? $uploadedFile['name'] : null,
                    isset($uploadedFile['type']) ? $uploadedFile['type'] : null
                    );
            } else {
                $subArray = [];
                foreach ($uploadedFile['error'] as $fileIdx => $error) {
                    // normalise subarray and re-parse to move the input's keyname up a level
                    $subArray[$fileIdx]['name'] = $uploadedFile['name'][$fileIdx];
                    $subArray[$fileIdx]['type'] = $uploadedFile['type'][$fileIdx];
                    $subArray[$fileIdx]['tmp_name'] = $uploadedFile['tmp_name'][$fileIdx];
                    $subArray[$fileIdx]['error'] = $uploadedFile['error'][$fileIdx];
                    $subArray[$fileIdx]['size'] = $uploadedFile['size'][$fileIdx];

                    $parsed[$field] = static::parseUploadedFiles($subArray);
                }
            }
        }
        return $parsed;
    }

    public function createUploadedFile($stringOrResourceOrStream, $size, $uploadErrorCode, $clientFilename = null, $clientMediaType = null)
    {
        if ($uploadErrorCode === UPLOAD_ERR_OK) {
            if (is_string($stringOrResourceOrStream)) {
                $stream = new Stream($stringOrResourceOrStream);
            } elseif (is_resource($stringOrResourceOrStream)) {
                $stream = new Stream($stringOrResourceOrStream);
            } elseif ($stringOrResourceOrStream instanceof StreamInterface) {
                $stream = $stringOrResourceOrStream;
            } else {
                throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
            }
        }
        if (! is_int($uploadErrorCode)
            || 0 > $uploadErrorCode
            || 8 < $uploadErrorCode
        ) {
            throw new InvalidArgumentException('Invalid upload error status for UploadedFile; must be some of UPLOAD_ERR_* constants');
        }
        $error = (new UploadedFileErrorEnum())($uploadErrorCode);
        
        if (! is_int($size)) {
            throw new InvalidArgumentException('Invalid size provided for UploadedFile; must be an int');
        }

        if (null !== $clientFilename && ! is_string($clientFilename)) {
            throw new InvalidArgumentException('Invalid client filename provided for UploadedFile; must be null or a string');
        }

        if (null !== $clientMediaType && ! is_string($clientMediaType)) {
            throw new InvalidArgumentException('Invalid client media type provided for UploadedFile; must be null or a string');
        }
        $this->uploadedFilesFactory->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }    
}
