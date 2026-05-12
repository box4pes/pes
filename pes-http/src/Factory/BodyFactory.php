<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Factory;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use Pes\Http\Environment;
use Pes\Http\Body;

/**
 * Create instances Body.
 */
class BodyFactory implements StreamFactoryInterface, EnvironmentAcceptInterface {

    /**
     *
     * @param Environment $environment
     * @return Body
     */
    public function createFromEnvironment(Environment $environment) {
        return $this->createStreamFromResource($environment->get(Environment::INPUT_STREAM));
    }

    /**
     *
     * @param string $content
     * @return Body
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = $this->createStreamFromFile('php://temp', 'r+');
        $stream->write($content);
        $stream->rewind();
        return $stream;
    }

    /**
     *
     * @param string $fileName
     * @param string $mode
     * @return Body
     */
    public function createStreamFromFile(string $fileName, string $mode = 'r'): StreamInterface
    {
        return $this->createStreamFromResource(fopen($fileName, $mode));
    }

    /**
     *
     * @param resource $resource
     * @return Body
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Body($resource);
    }
}
