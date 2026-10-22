<?php

/*
 * Copyright (C) 2017 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Pes\Http\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 *
 * @author pes2704
 */
interface MediaContentResolverInterface {

   /**
     * Vrací content type získaný z hlavičky Content-Type
     *
     * @param RequestInterface $request
     *
     * @return string|null Hodnota hlavičky Content-Type
     */
    public function getContentType(ServerRequestInterface $request);

    /**
     * Vrací media type získaný jako část content type
     *
     * @param RequestInterface $request
     *
     * @return string|null Hodnota media type
     */
    public function getMediaType(ServerRequestInterface $request);

    /**
     * Vrací parametry media type.
     *
     * @param RequestInterface $request
     *
     * @return array
     */
    public function getMediaTypeParams(ServerRequestInterface $request);

    /**
     * Veací znakovou sadu obsahu - získanou z parametrů media rype.
     *
     * @param RequestInterface $request
     *
     * @return string|null
     */
    public function getContentCharset(ServerRequestInterface $request);

    /**
     * Vrací velikosr obsahu získanou z hlavičky Content-Length.
     *
     * @param RequestInterface $request
     *
     * @return int|null
     */
    public function getContentLength(ServerRequestInterface $request);

}
