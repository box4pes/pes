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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Pes\Http\HeadersInterface;

/**
 * Response
 *
 * This class represents an HTTP response. It manages
 * the response status, headers, and body
 * according to the PSR-7 standard.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/MessageInterface.php
 * @link https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php
 */
class Response extends Message implements ResponseInterface
{
    /**
     * Status code
     *
     * @var int
     */
    protected $status = 200;

    /**
     * Reason phrase
     *
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * Status codes and reason phrases
     *
     * @var array
     */
    protected static $messages = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Create new HTTP response.
     *
     * @param int                   $status  The response status code.
     * @param HeadersInterface|null $headers The response headers.
     * @param StreamInterface|null  $body    The response body.
     * @param string                $reasonPhrase Non standard reason phrase.
     */
    public function __construct($status = 200, HeadersInterface $headers = null, StreamInterface $body = null, string $reasonPhrase = '')
    {
        if (!$this->isValidStatusCode($status)) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        } else {
            $this->status = $status;
        }
        $this->headers = $headers ? $headers : new Headers();
        $this->body = $body ? $body : new Body(fopen('php://temp', 'r+'));
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * This method is applied to the cloned object
     * after PHP performs an initial shallow-copy. This
     * method completes a deep-copy by creating new objects
     * for the cloned object's internal reference pointers.
     */
    public function __clone()
    {
        // PHP clone $xx klonuje $xx tak, že udělá jen mělkou (shallow) kopii, tedy všechny vlastnosti objektu $xx, které jsou objekty
        // a tedy obsahují reference na tyto objekty jsou pouze zkopírovány jako reference a tak vlastnosti klonovaného objektu obsahují tytéž objekty
        // jako původní objekt.
        // V metodě __clone() se pak musí naklonovat objektové vlastnosti, pokud nemají ukazovat na stejné objekty.
        // Zde se neklonuje objekt Body, protože to je objekt Stream a ten obsahuje vnitřně PHP stream ($this->stream typu resource), ten vnitřní PHP
        // stream by se příkazem clone neklonoval a klonovat objekt Stream při každém klonování je tak zbytečné.
        // Navíc klonování objektu, který má objektovou vlastnost typu stream způsobuje chybu, pokud se objekt klonuje a pak je ten původní destruován
        //  - naklonovaný stream zůstane, ale vznikají chyby - viz https://github.com/slimphp/Slim/issues/1860
        // Poznámka - v Requestu se v __clone klonuje i body (ale tam to je RequestBody (potomek Body)) funkcí stream_copy_to_stream().
        // Neklonovaný stream znamená, že Response není immutable (body není immutable) - http://www.php-fig.org/psr/psr-7/meta/#why-are-streams-mutable

        // function stream_copy_to_stream($source, $dest, int $maxlength = -1, int $offset = 0) - * @return int the total count of bytes copied.
        // maxlength určuje jak velkou paměť funkce spotřebuje
        // pro offset=0 stream_copy_to_stream udělá kopii od aktuální pozice zdrojového streamu - mohl by to být problém, pokud bych takto kopíroval
        // body v Response (zřejmě by bylo třaba dělat rewind($stream) PŘED kopírováním).
        // Navíc v Response se spíše než v Requestu může stát, že Body obsahuje hodně dat (dlouhý stream) - na druhou stranu asi v middleware
        // nevolám tak metodu Response->withStatus() - to je totiž jediná matoda withXXX v Response - jako metody withXXX v Requestu

        $this->headers = clone $this->headers;
    }

    /*******************************************************************************
     * Status
     ******************************************************************************/

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int $code HTTP result status code to set. Must be he 3-digit integer from 100 to 599.
     * @param string $reasonPhrase The reason phrase to use with the provided status code;
     *      if none is provided, the default reason phrase as suggested in the HTTP specification is used.
     *
     * @return \Pes\Http\Response
     * @throws InvalidArgumentException Form invalid HTTP status code and invalid reason phrase
     *      and for status code which not specifiad in HTTP specification with no supplied reason phrase.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        if (!$this->isValidStatusCode($code)) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }
        if (!$this->isValidReasonPhrase($reasonPhrase)) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }
        $clone = clone $this;
        $clone->status = $code;
        if ($reasonPhrase === '' && isset(static::$messages[$code])) {
            $reasonPhrase = static::$messages[$code];
        }

        if ($reasonPhrase === '') {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
        }

        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Validate HTTP status code. Code must be integer with value from 100 to 599.
     * @param int $status
     * @return boolean
     */
    protected function isValidStatusCode($status)
    {
        return is_integer($status) AND $status>=100 AND $status<=599 ? TRUE : FALSE;
    }

    /**
     * Validate reason phrase. Reason phrase must be string or object convertible to string (has __toString() method).
     * @param type $reasonPhrase
     * @return type
     */
    protected function isValidReasonPhrase($reasonPhrase) {
        return is_string($reasonPhrase) OR method_exists($reasonPhrase, '__toString') ? TRUE : FALSE;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        if ($this->reasonPhrase) {
            $phrase = $this->reasonPhrase;
        } elseif (isset(static::$messages[$this->status])) {
            $phrase = static::$messages[$this->status];
        } else {
            $phrase = '';
        }
        return $phrase;
    }

}
