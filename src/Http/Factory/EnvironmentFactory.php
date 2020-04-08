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

use Pes\Http\Environment;

/**
 * Description of EnvironmentFactory
 *
 * @author pes2704
 */
class EnvironmentFactory implements EnvironmentFactoryInterface {

    /**
     * {@inheritdoc}
     */
    public function createFromGlobals() : Environment {
        return $this->create($_SERVER, $this->getcopyOfInput());
    }

    /**
     * {@inheritdoc}
     */
    public function createFromServerParams(array $serverParams = array()) : Environment {
        return $this->create($serverParams, $this->getcopyOfInput());
    }

    private function getcopyOfInput() {
        // kopie php://input do streamu:
        $stream = fopen('php://temp', 'w+');  // php://temp will store its data in memory but will use a temporary file once the amount of data stored hits a predefined limit (the default is 2 MB). The location of this temporary file is determined in the same way as the sys_get_temp_dir() function.
        // function stream_copy_to_stream($source, $dest, int $maxlength = -1, int $offset = 0) - * @return int the total count of bytes copied.
        // maxlength určuje jak velkou paměť funkce spotřebuje
        // pro offset=0 stream_copy_to_stream udělá kopii od aktuální pozice zdrojového streamu - tady to nevadí, zdroj byl právě otevřen fopen,
        // ale mohl by to být problém, pokud bys takto kopíroval body v Response (! rewind($stream) PŘED kopírováním)
        stream_copy_to_stream(fopen('php://input', 'r'), $stream);
        rewind($stream);
        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $serverParams, $inputStream) : Environment {
        if (!is_resource($inputStream)) {
            throw new \UnexpectedValueException("Parametr \$inpustream musí báýt tyou resource.");
        }
        $environment = new Environment($serverParams, $inputStream);
        $authorization = $environment->get('HTTP_AUTHORIZATION');

        if (null === $authorization && is_callable('getallheaders')) {
            $headers = getallheaders();
            $headers = array_change_key_case($headers, CASE_LOWER);
            if (isset($headers['authorization'])) {
                $environment->set('HTTP_AUTHORIZATION', $headers['authorization']);
            }
        }

        return $environment;
    }
}
