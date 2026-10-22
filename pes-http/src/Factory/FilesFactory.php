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
use Pes\Http\UploadedFileErrorEnum;

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
     * @return array A normalized tree of UploadedFile instances or null if none are provided.
     */
    public function createFiles() {
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
    private function parseUploadedFiles(array $uploadedFiles) {
        $parsed = [];
        foreach ($uploadedFiles as $field => $uploadedFile) {
            // Pokud prvek v poli nemá klíč error, znamená to, že $_FILES je vícerozměrné pole
            if (!isset($uploadedFile['error'])) {
                if (is_array($uploadedFile)) {
                    $parsed[$field] = static::parseUploadedFiles($uploadedFile);
                }
                continue;
            }

            $parsed[$field] = [];
            // Pokud error není pole, jedná se o upload jednoho souboru
            if (!is_array($uploadedFile['error'])) {
                $parsed[$field] = new UploadedFile(
                    $uploadedFile['tmp_name'],              // předávám filename
                    isset($uploadedFile['size']) ? $uploadedFile['size'] : null,
                        $uploadedFile['error'],
                    isset($uploadedFile['name']) ? $uploadedFile['name'] : null,
                    isset($uploadedFile['type']) ? $uploadedFile['type'] : null
                    );
            } else {
                // Pokud bylo upload více souborů přes jedno pole (např. <input name="dokumenty[]" type="file" multiple>), 
                // PHP vytvoří v $_FILES 
                //    $_FILES['dokumenty']['name'][0], $_FILES['dokumenty']['name'][1] ...
                //
                // Tento kód přeskupí (normalizuje) je do logického pole, kde má každý soubor své vlastní ucelené informace:
                // Následně toto očištěné podpole znovu předá rekurzivně metodě parseUploadedFiles(), která z jednotlivých prvků vytvoří objekty UploadedFile.                
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

    private function createUploadedFile($filepath, $size, $uploadErrorCode, $clientFilename = null, $clientMediaType = null) {
        new UploadedFile($filepath, $size, $uploadErrorCode, $clientFilename, $clientMediaType);
    }    
}
