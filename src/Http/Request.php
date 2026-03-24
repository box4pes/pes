<?php
/**
 * upraveno z:
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2016 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Pes\Http;

use InvalidArgumentException;
use RuntimeException;

use Pes\Collection\MapCollection;
use Pes\Http\HeadersInterface;
use Pes\Http\Request\MediaContentResolverInterface;
use Pes\Http\Request\MediaContentResolver;
use Pes\Http\Body\BodyParserInterface;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Request
 *
 * This class represents an HTTP request. It manages
 * the request method, URI, headers, cookies, and body
 * according to the PSR-7 standard.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
 * @link https://github.com/php-fig/http-message/blob/master/src/RequestInterface.php
 * @link https://github.com/php-fig/http-message/blob/master/src/ServerRequestInterface.php
 */
class Request extends Message implements ServerRequestInterface
{
    /**
     * The oroginal request method
     *
     * @var string
     */
    protected $originalMethod;

    /**
     * The request method
     *
     * @var string
     */
    protected $method;

    /**
     * The request URI object
     *
     * @var Psr\Http\Message\UriInterface
     */
    protected $uri;

    protected $headers;

    /**
     * The request URI target (path + query string)
     *
     * @var string
     */
    protected $requestTarget;

    /**
     * The request query string params
     *
     * @var array
     */
    protected $queryParams;

    /**
     * The request cookies
     *
     * @var array
     */
    protected $cookies;

    /**
     * The server environment variables at the time the request was created.
     *
     * @var array
     */
    protected $serverParams;

    /**
     * The request attributes (route segment names and values)
     *
     * @var \Pes\Collection\MapCollection
     */
    protected $attributes;

    protected $body;

    /**
     * The request body parsed (if possible) into a PHP array or object
     *
     * @var null|array|object
     */
    protected $bodyParsed = false;

    /**
     * List of request body parsers (e.g., url-encoded, JSON, XML, multipart)
     *
     * @var callable[]
     */
    protected $bodyParsers = [];

    /**
     * List of uploaded files
     *
     * @var UploadedFileInterface[]
     */
    protected $uploadedFiles;

    /**
     * Objekt RequestMediaContentInterface
     *
     * @var RequestMediaContentInterface
     */
    private $requestMediaContentResolver;

    /**
     * Objekt BodyParserInterface
     *
     * @var BodyParserInterface
     */
    private $bodyParser;

    /**
     * Valid request methods
     *
     * @var string[]
     */
    protected $validMethods = [
        'CONNECT' => 1,
        'DELETE' => 1,
        'GET' => 1,
        'HEAD' => 1,
        'OPTIONS' => 1,
        'PATCH' => 1,
        'POST' => 1,
        'PUT' => 1,
        'TRACE' => 1,
    ];

    /**
     * HTTP request.
     *
     * Přidává hlavičku "host", pokud v requestu není obsažena a je definován host v uri.
     * Pokud nebyl zadán media parser (body parser), metoda requestu je POST a media type (část content-type) je 'application/x-www-form-urlencoded' nebo 'multipart/form-data'
     * použije defaultní media parser BodyParserPostCopy(). Ten pro uvedenou situaci vždy jako parsed body vrací $_POST (viz PSR-7).
     *
     * @param string           $method        The request method
     * @param UriInterface     $uri           The request URI object
     * @param HeadersInterface $headers       The request headers collection
     * @param array            $cookies       The request cookies collection
     * @param array            $serverParams  The server environment object
     * @param StreamInterface  $body          The request body object
     * @param array            $uploadedFiles The request uploadedFiles collection
     * @param MediaContentResolverInterface|null $requestMediaContentResolver
     * @param BodyParserInterface|null $bodyParser
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        HeadersInterface $headers,
        array $cookies,
        array $serverParams,
        StreamInterface $body,
        array $uploadedFiles = [],
        ?MediaContentResolverInterface $requestMediaContentResolver= null,
        ?BodyParserInterface $bodyParser=null
    ) {
        $this->originalMethod = $this->filterMethod($method);
        $this->uri = $uri;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->serverParams = $serverParams;
        $this->attributes = new MapCollection();  // Kolekce pRo přídavné, uživatelské atributy
        $this->body = $body;
        $this->uploadedFiles = $uploadedFiles;
        $this->requestMediaContentResolver = $requestMediaContentResolver ? : new MediaContentResolver();
        $this->bodyParser = $bodyParser ? : new Body\BodyParser();

        if (isset($this->serverParams['SERVER_PROTOCOL'])) {
            $this->protocolVersion = str_replace('HTTP/', '', $this->serverParams['SERVER_PROTOCOL']);  // zdá, že protocolVersion není nikde použito SVOBODA
        }

        if (!$this->headers->has('Host') || $this->uri->getHost() !== '') {
            $this->headers->set('Host', $this->uri->getHost());
        }

    }

    /**
     * This method is applied to the cloned object
     * after PHP performs an initial shallow-copy. This
     * method completes a deep-copy by creating new objects
     * for the cloned object's internal reference pointers.
     */
    public function __clone()
    {
        // objektové vlastnosti definované v RequestPsr: $this->uri,$this->attributes
        // uri neklonuji (nemění se žádnou metodou thisXXX), attributes klonuji (mení se withAttribute, withAttributes)
        $this->attributes = clone $this->attributes;  // MapCollection, potomek CollectionAbstract - ta má metodu __clone()
        // objektové vlastnosti definované v Message: $this->headers, $this->body StreamInterface, tedy Stream objekt a ten má
        // objektovou vlastnost ->stream (což je resource) - klonování clone $this->body v takovém případě sice naklonuje objekt body,
        // ale už neklonuje v něm obsaženou vlastnost ->body
        // klonuji headers a body - podle Psr a implementováno zde, v RequestPsr je body
        $this->headers = clone $this->headers;  // MapCollection, potomek CollectionAbstract - ta má metodu __clone()
        $this->body = clone $this->body;
    }

    /*******************************************************************************
     * Method
     ******************************************************************************/

    #[\Override]
    public function getMethod(): string {
        if ($this->method === null) {
            $this->method = $this->originalMethod;
            $customMethod = $this->getHeaderLine('X-Http-Method-Override');

            if ($customMethod) {
                $this->method = $this->filterMethod($customMethod);
            } elseif ($this->originalMethod === 'POST') {
                $parsedBody = $this->getParsedBody();

                if (is_object($parsedBody) && property_exists($parsedBody, '_METHOD')) {
                    $this->method = $this->filterMethod((string)$parsedBody->_METHOD);
                } elseif (is_array($parsedBody) && isset($parsedBody['_METHOD'])) {
                    $this->method = $this->filterMethod((string)$parsedBody['_METHOD']);
                }
            }
        }

        return $this->method;
    }

    #[\Override]
    public function withMethod(string $method): RequestInterface {
        $method = $this->filterMethod($method);
        $clone = clone $this;
        $clone->originalMethod = $method;
        $clone->method = $method;

        return $clone;
    }

    /**
     * Validate the HTTP method
     * 
     * @param string|null $method
     * @return string|null
     * @throws InvalidArgumentException on invalid HTTP method.
     */
    protected function filterMethod(?string $method): ?string
    {
        if ($method === null) {
            return $method;
        }

        if (!is_string($method)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method; must be a string, received %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }

        $method = strtoupper($method);
        if (!isset($this->validMethods[$method])) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method "%s" provided',
                $method
            ));
        }

        return $method;
    }


    /*******************************************************************************
     * URI
     ******************************************************************************/

    #[\Override]
    public function getRequestTarget(): string {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        if ($this->uri === null) {
            return '/';
        }

        $path = $this->uri->getPath();
        $query = $this->uri->getQuery();
        if ($query) {
            $path .= '?' . $query;
        }
        $this->requestTarget = $path;

        return $this->requestTarget;
    }

    #[\Override]
    public function withRequestTarget(string $requestTarget): RequestInterface {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    #[\Override]
    public function getUri(): UriInterface {
        return $this->uri;
    }

    #[\Override]
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost) {
            if ($uri->getHost() !== '') {
                $clone->headers->set('Host', $uri->getHost());
            }
        } else {
            if ($this->uri->getHost() !== '' && (!$this->hasHeader('Host') || $this->getHeader('Host') === null)) {
                $clone->headers->set('Host', $uri->getHost());
            }
        }

        return $clone;
    }

    /*******************************************************************************
     * Cookies
     ******************************************************************************/

    #[\Override]
    public function getCookieParams(): array {
        return $this->cookies;
    }

    #[\Override]
    public function withCookieParams(array $cookies): ServerRequestInterface {
        $clone = clone $this;
        $clone->cookies = $cookies;

        return $clone;
    }

    /*******************************************************************************
     * Query Params
     ******************************************************************************/

    #[\Override]
    public function getQueryParams(): array {
        if (is_array($this->queryParams)) {
            return $this->queryParams;
        }

        if ($this->uri === null) {
            return [];
        }

        parse_str($this->uri->getQuery(), $this->queryParams); // <-- URL decodes data

        return $this->queryParams;
    }

    #[\Override]
    public function withQueryParams(array $query): ServerRequestInterface {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /*******************************************************************************
     * File Params
     ******************************************************************************/

    #[\Override]
    public function getUploadedFiles(): array {
        return $this->uploadedFiles;
    }

    #[\Override]
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /*******************************************************************************
     * Server Params
     ******************************************************************************/

    #[\Override]
    public function getServerParams(): array {
        return $this->serverParams;
    }

    /*******************************************************************************
     * Attributes
     ******************************************************************************/

    #[\Override]
    public function getAttributes(): array {
        return $this->attributes->getArrayCopy();
    }

    #[\Override]
    public function getAttribute(string $name, $default = null): mixed {
        return $this->attributes->get($name, $default);
    }

    #[\Override]
    public function withAttribute(string $name, $value): ServerRequestInterface {
        $clone = clone $this;
        $clone->attributes->set($name, $value);

        return $clone;
    }

    /**
     * Create a new instance with the specified derived request attributes.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method allows setting all new derived request attributes as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * updated attributes.
     *
     * @param  array $attributes New attributes
     * @return self
     */
    public function withAttributes(array $attributes): ServerRequestInterface
    {
        $clone = clone $this;
        $clone->attributes = new MapCollection($attributes);

        return $clone;
    }

    #[\Override]
    public function withoutAttribute(string $name): ServerRequestInterface {
        $clone = clone $this;
        $clone->attributes->remove($name);

        return $clone;
    }

    /*******************************************************************************
     * Body
     ******************************************************************************/

    #[\Override]
    public function getParsedBody() {
        if ($this->bodyParsed) {
            return $this->bodyParsed;
        }

        if (!$this->body) {
            return null;
        }

        if (!$this->bodyParser) {
            throw new \LogicException("Objekt nemá nastaven body parser. Body parser musí být zadán jako parametr konstruktoru nebo je použit defaultní parse pro metodu POST.");
        }
        $currentPointerPosition = $this->getBody()->tell();
        $this->bodyParsed = $this->bodyParser->parse($this);
        if ($currentPointerPosition != $this->getBody()->tell()) {
            $this->getBody()->seek($currentPointerPosition);
        }

        return $this->bodyParsed;

    }

    #[\Override]
    public function withParsedBody($data): ServerRequestInterface {
        if (!is_null($data) && !is_object($data) && !is_array($data)) {
            throw new InvalidArgumentException('Parsed body value must be an array, an object, or null');
        }

        $clone = clone $this;
        $clone->bodyParsed = $data;

        return $clone;
    }

}
