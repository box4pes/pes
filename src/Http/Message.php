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
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Abstract message (base class for Request and Response)
 *
 * This class represents a general HTTP message. It provides common properties and methods for
 * the HTTP request and response, as defined in the PSR-7 MessageInterface.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
 * @see Pes\Http\Request
 * @see Pes\Http\Response
 */
abstract class Message implements MessageInterface
{
    /**
     * Protocol version
     *
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * A map of valid protocol versions
     *
     * @var array
     */
    protected static $validProtocolVersions = [
        '1.0' => true,
        '1.1' => true,
        '2.0' => true,
    ];

    /**
     * Headers
     *
     * @var HeadersInterface
     */
    protected $headers;

    /**
     * Body object
     *
     * @var StreamInterface
     */
    protected $body;


    /**
     * Disable magic setter to ensure immutability
     */
    public function __set($name, $value)
    {
        user_error('Pokus o nastavení nedefinované vlastnosti '.$name.' objektu Message. Objekt je immutable a má zakázanou magickou metodu __set().', E_USER_WARNING);
    }

    /*******************************************************************************
     * Protocol
     ******************************************************************************/

    #[\Override]
    public function getProtocolVersion(): string {
        return $this->protocolVersion;
    }

    #[\Override]
    public function withProtocolVersion(string $version): MessageInterface {
        if (!isset(self::$validProtocolVersions[$version])) {
            throw new InvalidArgumentException(
                'Invalid HTTP version. Must be one of: '
                . implode(', ', array_keys(self::$validProtocolVersions))
            );
        }
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /*******************************************************************************
     * Headers
     ******************************************************************************/

    #[\Override]
    public function getHeaders(): array {
        return $this->headers->all();
    }

    #[\Override]
    public function hasHeader(string $name): bool {
        return $this->headers->has($name);
    }

    #[\Override]
    public function getHeader(string $name): array {
        return $this->headers->get($name);
    }

    #[\Override]
    public function getHeaderLine(string $name): string {
        return implode(',', $this->headers->get($name));
    }

    #[\Override]
    public function withHeader(string $name, $value): MessageInterface {
        $clone = clone $this;
        $clone->headers->set($name, $value);

        return $clone;
    }

    #[\Override]
    public function withAddedHeader(string $name, $value): MessageInterface {
        $clone = clone $this;
        $clone->headers->appendValue($name, $value);

        return $clone;
    }

    #[\Override]
    public function withoutHeader(string $name): MessageInterface {
        $clone = clone $this;
        $clone->headers->remove($name);

        return $clone;
    }

    /*******************************************************************************
     * Body
     ******************************************************************************/

    #[\Override]
    public function getBody(): StreamInterface {
        return $this->body;
    }

    #[\Override]
    public function withBody(StreamInterface $body): MessageInterface {
        // TODO: Test for invalid body?
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }
}
