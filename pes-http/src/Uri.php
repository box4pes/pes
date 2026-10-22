<?php
/**
 * Upraveno z:
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2016 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Pes\Http;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

/**
 * Value object representing a URI, implements UriInterface.
 *
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * Typically the Host header will be also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Uri implements UriInterface
{
    /**
     * Uri scheme (without "://" suffix)
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * Uri user
     *
     * @var string
     */
    protected $user = '';

    /**
     * Uri password
     *
     * @var string
     */
    protected $password = '';

    /**
     * Uri host
     *
     * @var string
     */
    protected $host = '';

    /**
     * Uri port number
     *
     * @var null|int
     */
    protected $port;

    /**
     * Uri base path
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Uri path
     *
     * @var string
     */
    protected $path = '';

    /**
     * Uri query string (without "?" prefix)
     *
     * @var string
     */
    protected $query = '';

    /**
     * Uri fragment string (without "#" prefix)
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * Create new Uri.
     * 
     * @param string $scheme   Uri scheme.
     * @param string $host     Uri host.
     * @param int|null $port     Uri port number.
     * @param string|null $path     Uri path.
     * @param string|null $query    Uri query string.
     * @param string|null $fragment Uri fragment.
     * @param string|null $user     Uri user.
     * @param string|null $password Uri password.
     */
    public function __construct(
        string $scheme,
        string $host,
        ?int $port = null,
        ?string $path = '/',
        ?string $query = '',
        ?string $fragment = '',
        ?string $user = '',
        ?string $password = ''
    ) {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $this->filterPort($port);
        $this->path = empty($path) ? '/' : $this->filterPath($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = $user;
        $this->password = $password;
    }

    /********************************************************************************
     * Scheme
     *******************************************************************************/

    /**
     * {@inheritDoc}
     * 
     * @return string
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    #[\Override]
    public function getScheme(): string
    {
        return $this->scheme;
    }


    #[\Override]
    public function withScheme(string $scheme): UriInterface
    {
        $clone = clone $this;
        $clone->scheme = $this->filterScheme($scheme);

        return $clone;
    }

    /**
     * Filter Uri scheme.
     *
     * @param  string $scheme Raw Uri scheme.
     * @return string
     *
     * @throws InvalidArgumentException If the Uri scheme is not a string.
     * @throws InvalidArgumentException If Uri scheme is not "", "https", or "http".
     */
    protected function filterScheme($scheme)
    {
        static $valid = [
            '' => true,
            'https' => true,
            'http' => true,
        ];

        if (!is_string($scheme) && !method_exists($scheme, '__toString')) {
            throw new InvalidArgumentException('Uri scheme must be a string');
        }

        $schemeNormalized = str_replace('://', '', strtolower((string)$scheme));
        if (!isset($valid[$schemeNormalized])) {
            throw new InvalidArgumentException('Uri scheme must be one of: "'. \implode('", "', array_keys($valid)).'".');
        }

        return $schemeNormalized;
    }

    /********************************************************************************
     * Authority
     *******************************************************************************/

    #[\Override]
    public function getAuthority(): string
    {
        $userInfo = $this->getUserInfo();
        $host = $this->getHost();
        $port = $this->getPort();

        return ($userInfo ? $userInfo . '@' : '') . $host . ($port !== null ? ':' . $port : '');
    }

    #[\Override]
    public function getUserInfo(): string
    {
        return $this->user . ($this->password ? ':' . $this->password : '');
    }

    #[\Override]
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password ? $password : '';

        return $clone;
    }

    #[\Override]
    public function getHost(): string
    {
        return $this->host;
    }

    #[\Override]
    public function withHost(string $host): UriInterface
    {
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    #[\Override]
    public function getPort(): ?int
    {
        return $this->port && !$this->hasStandardPort() ? $this->port : null;
    }

    #[\Override]
    public function withPort(?int $port): UriInterface
    {
        $port = $this->filterPort($port);
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * Does this Uri use a standard port?
     *
     * @return bool
     */
    protected function hasStandardPort()
    {
        return ($this->scheme === 'http' && $this->port === 80) || ($this->scheme === 'https' && $this->port === 443);
    }

    /**
     * Filter Uri port.
     *
     * @param  null|int $port The Uri port number.
     * @return null|int
     *
     * @throws InvalidArgumentException If the port is invalid.
     */
    protected function filterPort($port)
    {
        if (is_null($port) || (is_integer($port) && ($port >= 1 && $port <= 65535))) {
            return $port;
        }

        throw new InvalidArgumentException('Uri port must be null or an integer between 1 and 65535 (inclusive)');
    }

    /********************************************************************************
     * Path
     *******************************************************************************/

    #[\Override]
    public function getPath(): string
    {
        return $this->path;
    }

    #[\Override]
    public function withPath(string $path): UriInterface
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException('Uri path must be a string');
        }

        $clone = clone $this;
        $clone->path = $this->filterPath($path);

        // if the path is absolute, then clear basePath
        if (substr($path, 0, 1) == '/') {
            $clone->basePath = '';
        }
        return $clone;
    }

    /**
     * Filter Uri path.
     *
     * This method percent-encodes all reserved
     * characters in the provided path string. This method
     * will NOT double-encode characters that are already
     * percent-encoded.
     *
     * @param  string $path The raw uri path.
     * @return string       The RFC 3986 percent-encoded uri path.
     * @link   http://www.faqs.org/rfcs/rfc3986.html
     */
    protected function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /********************************************************************************
     * Query
     *******************************************************************************/

    #[\Override]
    public function getQuery(): string
    {
        return $this->query;
    }

    #[\Override]
    public function withQuery(string $query): UriInterface
    {
        if (!is_string($query) && !method_exists($query, '__toString')) {
            throw new InvalidArgumentException('Uri query must be a string');
        }
        $query = ltrim((string)$query, '?');
        $clone = clone $this;
        $clone->query = $this->filterQuery($query);

        return $clone;
    }

    /**
     * Filters the query string or fragment of a URI.
     *
     * @param string $query The raw uri query string.
     * @return string The percent-encoded query string.
     */
    protected function filterQuery($query)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $query
        );
    }

    /********************************************************************************
     * Fragment
     *******************************************************************************/

    #[\Override]
    public function getFragment(): string
    {
        return $this->fragment;
    }

    #[\Override]
    public function withFragment(string $fragment): UriInterface
    {
        if (!is_string($fragment) && !method_exists($fragment, '__toString')) {
            throw new InvalidArgumentException('Uri fragment must be a string');
        }
        $fragment = ltrim((string)$fragment, '#');
        $clone = clone $this;
        $clone->fragment = $this->filterQuery($fragment);

        return $clone;
    }

    #[\Override]
    public function __toString(): string
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
//        $basePath = $this->getBasePath();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

//        $path = $basePath . '/' . ltrim($path, '/');

        return ($scheme ? $scheme . ':' : '')
            . ($authority ? '//' . $authority : '')
            . $path
            . ($query ? '?' . $query : '')
            . ($fragment ? '#' . $fragment : '');
    }

###################################################################
    #      * Note: This method is not part of the PSR-7 standard.
###################################################################


    /**
     * Retrieve the base path segment of the URI.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method MUST return a string; if no path is present it MUST return
     * an empty string.
     *
     * @return string The base path segment of the URI.
     */
//    public function getBasePath()
//    {
//        return $this->basePath;
//    }

    /**
     * Set base path.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param  string $basePath
     * @return self
     */
//    public function withBasePath($basePath)
//    {
//        if (!is_string($basePath)) {
//            throw new InvalidArgumentException('Uri path must be a string');
//        }
//        if (!empty($basePath)) {
//            $basePath = '/' . trim($basePath, '/'); // <-- Trim on both sides
//        }
//        $clone = clone $this;
//
//        if ($basePath !== '/') {
//            $clone->basePath = $this->filterPath($basePath);
//        }
//
//        return $clone;
//    }

    /**
     * Return the fully qualified base URL.
     *
     * Note that this method never includes a trailing /
     *
     * This method is not part of PSR-7.
     *
     * @return string
     */
//    public function getBaseUrl()
//    {
//        $scheme = $this->getScheme();
//        $authority = $this->getAuthority();
//        $basePath = $this->getBasePath();
//
//        if ($authority && substr($basePath, 0, 1) !== '/') {
//            $basePath = $basePath . '/' . $basePath;
//        }
//
//        return ($scheme ? $scheme . ':' : '')
//            . ($authority ? '//' . $authority : '')
//            . rtrim($basePath, '/');
//    }
}
