<?php
namespace Pes\Http\Body;

use Psr\Http\Message\ServerRequestInterface;

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 *
 * @author pes2704
 */
interface BodyParserInterface {
    /**
     * Registruje media parser.
     *
     * @param string   $mediaType A HTTP media type (bez ostatních parametrů content-type.
     * @param callable $callable  Callable, která vrací obsah parsovaný podle media type.
     */
    public function registerMediaParser($mediaType, callable $callable);
    
    /**
     * Vrací parsovaný obsah body. Pokud body neexistuje, MUSÍ vracet NULL.
     * @param ServerRequestInterface $request
     */
    public function parse(ServerRequestInterface $request);

}
