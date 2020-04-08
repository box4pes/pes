<?php
namespace Pes\Http\Body;

use Psr\Http\Message\ServerRequestInterface;
use Pes\Http\Request\MediaContentResolverInterface;
use Pes\Http\Request\MediaContentResolver;

/*
 * Copyright (C) 2018 pes2704
 *
 * This is no software. This is quirky text and you may do anything with it, if you like doing
 * anything with quirky texts. This text is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Description of BodyParser
 *
 * @author pes2704
 */
class BodyParser implements BodyParserInterface {

    /**
     *
     * @var RequestMediaContentInterface
     */
    private $mediaContentResolver;


    public function __construct(MediaContentResolverInterface $requestMediaContentResolver=NULL) {

        $this->mediaContentResolver = $requestMediaContentResolver ? $requestMediaContentResolver : new MediaContentResolver();

// $_POST - An associative array of variables passed to the current script via the HTTP POST method when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request.
//    if ($method === 'POST' && in_array($mediaContentResolver->getMediaType($headers), ['application/x-www-form-urlencoded', 'multipart/form-data'])) {

        $this->registerMediaParser('application/x-www-form-urlencoded', function ($input) {
            return $_POST;
//https://stackoverflow.com/questions/5077969/php-some-post-values-missing-but-are-present-in-php-input
//            parse_str($input, $data);
//            return $data;
        });

        $this->registerMediaParser('multipart/form-data', function ($input) {
//            https://stackoverflow.com/questions/1075513/php-parsing-multipart-form-data
//             multipart parser:
//            https://github.com/h4cc/multipart
            return $_POST;
        });

        $this->registerMediaParser('application/json', function ($input) {
            return json_decode($input, true);
        });

        $this->registerMediaParser('application/xml', function ($input) {
            $backup = libxml_disable_entity_loader(true);
            $result = simplexml_load_string($input);
            libxml_disable_entity_loader($backup);
            return $result;
        });

        $this->registerMediaParser('text/xml', function ($input) {
            $backup = libxml_disable_entity_loader(true);
            $result = simplexml_load_string($input);
            libxml_disable_entity_loader($backup);
            return $result;
        });

    }

    /**
     * Registruje media parser.
     *
     * @param string   $mediaType A HTTP media type (bez ostatních parametrů content-type.
     * @param callable $callable  Callable, která vrací obsah parsovaný podle media type.
     */
    public function registerMediaParser($mediaType, callable $callable) {
        if ($callable instanceof \Closure) {
            $callable = $callable->bindTo($this);
        }
        $this->bodyParsers[(string)$mediaType] = $callable;
    }

    /**
     * {@inheritdoc}
     * Parsování body provede pomocí media parseru registrovaného pro nedia typ requestu. Pokud není zaregistrován vhodný media type parser, metoda vrací NULL.
     *
     * @param ServerRequestInterface $request
     * @return type
     * @throws RuntimeException
     */
    public function parse(ServerRequestInterface $request) {
        $mediaType = $this->mediaContentResolver->getMediaType($request);
// $_POST - An associative array of variables passed to the current script via the HTTP POST method when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request.
//    if (in_array($mediaType, ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
//        return $_POST;
//    }
        if($mediaType) {
            // look for a media type with a structured syntax suffix (RFC 6839)
            $parts = explode('+', $mediaType);
            if (count($parts) >= 2) {
                $mediaType = 'application/' . $parts[count($parts)-1];
            }

            if (isset($this->bodyParsers[$mediaType]) === true) {
                $body = (string)$request->getBody();
                $parsed = $this->bodyParsers[$mediaType]($body);

                if (!is_null($parsed) && !is_object($parsed) && !is_array($parsed)) {
                    throw new RuntimeException(
                        "Návratová hodnota media parseru registrovaného pro {$mediaType} není povolena. Musí být array nebo object nebo null."
                    );
                }
                return $parsed;
            } else {
                user_error("Není zaregistrován media parser pro nalezený media typ {$mediaType}.", E_USER_WARNING);
            }
        }
        return null;
    }

    /**
     * Vrací media type získaný jako část content type
     *
     * @param RequestInterface $request
     *
     * @return string|null Hodnota media type
     */
    private function getMediaType(ServerRequestInterface $request) {
        $contentType = $this->getContentType($request);
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);

            return strtolower($contentTypeParts[0]);
        }

        return null;
    }

    /**
     * Vrací content type získaný z hlavičky Content-Type
     *
     * @param RequestInterface $request
     *
     * @return string|null Hodnota hlavičky Content-Type
     */
    private function getContentType(ServerRequestInterface $request) {
        $result = $this->getHeader('Content-Type');

        return $result ? $result[0] : null;
    }

}
