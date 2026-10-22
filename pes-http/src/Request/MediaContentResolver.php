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
 * Description of Request
 *
 * @author pes2704
 */
class MediaContentResolver implements MediaContentResolverInterface {

    /**
     * Vrací content type získaný z hlavičky Content-Type
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null Hodnota hlavičky Content-Type
     */
    public function getContentType(ServerRequestInterface $request) {
        $result = $request->getHeader('Content-Type');

        return $result ? $result[0] : null;
    }

    /**
     * Vrací media type získaný jako část content type
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null Hodnota media type
     */
    public function getMediaType(ServerRequestInterface $request) {
        $contentType = $this->getContentType($request);
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);

            return strtolower($contentTypeParts[0]);
        }

        return null;
    }

    /**
     * Vrací parametry media type.
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getMediaTypeParams(ServerRequestInterface $request) {
        $contentType = $this->getContentType($request);
        $contentTypeParams = [];
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            $contentTypePartsLength = count($contentTypeParts);
            for ($i = 1; $i < $contentTypePartsLength; $i++) {
                $paramParts = explode('=', $contentTypeParts[$i]);
                $contentTypeParams[strtolower($paramParts[0])] = $paramParts[1];
            }
        }

        return $contentTypeParams;
    }

    /**
     * Veací znakovou sadu obsahu - získanou z parametrů media rype.
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function getContentCharset(ServerRequestInterface $request) {
        $mediaTypeParams = $this->getMediaTypeParams($request);
        if (isset($mediaTypeParams['charset'])) {
            return $mediaTypeParams['charset'];
        }

        return null;
    }

    /**
     * Vrací velikosr obsahu získanou z hlavičky Content-Length.
     *
     * @param ServerRequestInterface $request
     *
     * @return int|null
     */
    public function getContentLength(ServerRequestInterface $request) {
        $result = $request->getHeader('Content-Length');

        return $result ? (int)$result[0] : null;
    }

}
