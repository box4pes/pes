<?php

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http;

use Pes\Type\Enum;

/**
 * Description of UploadedFileErrorEnum
 *
 * @author pes2704
 */
class UploadedFileErrorEnum   extends Enum {
    const UPLOAD_ERR_OK = UPLOAD_ERR_OK;
    const UPLOAD_ERR_INI_SIZE = UPLOAD_ERR_INI_SIZE;
    const UPLOAD_ERR_FORM_SIZE = UPLOAD_ERR_FORM_SIZE;
    const UPLOAD_ERR_PARTIAL = UPLOAD_ERR_PARTIAL;
    const UPLOAD_ERR_NO_FILE = UPLOAD_ERR_NO_FILE;
    const UPLOAD_ERR_NO_TMP_DIR = UPLOAD_ERR_NO_TMP_DIR;
    const UPLOAD_ERR_CANT_WRITE = UPLOAD_ERR_CANT_WRITE;
    const UPLOAD_ERR_EXTENSION = UPLOAD_ERR_EXTENSION;
}