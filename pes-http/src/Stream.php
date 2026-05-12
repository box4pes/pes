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
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Represents a data stream as defined in PSR-7.
 *
 * @link https://github.com/php-fig/http-message/blob/master/src/StreamInterface.php
 */
class Stream implements StreamInterface
{
    /**
     * Bit mask to determine if the stream is a pipe
     *
     * This is octal as per header stat.h
     */
    const FSTAT_MODE_S_IFIFO = 0010000;

    /**
     * Resource modes
     *
     * @var  array
     * @link http://php.net/manual/function.fopen.php
     */
    protected static $modes = [
        'readable' => ['r', 'r+', 'w+', 'a+', 'x+', 'c+'],
        'writable' => ['r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+'],
    ];

    /**
     * The underlying stream resource
     *
     * @var resource
     */
    protected $stream;

    /**
     * Stream metadata
     *
     * @var array
     */
    protected $meta;

    /**
     * Is this stream readable?
     *
     * @var bool
     */
    protected $readable;

    /**
     * Is this stream writable?
     *
     * @var bool
     */
    protected $writable;

    /**
     * Is this stream seekable?
     *
     * @var bool
     */
    protected $seekable;

    /**
     * The size of the stream if known
     *
     * @var null|int
     */
    protected $size;

    /**
     * Is this stream a pipe?
     *
     * @var bool
     */
    protected $isPipe;

    /**
     * Create a new Stream.
     *
     * @param  resource $stream A PHP resource handle.
     *
     * @throws InvalidArgumentException If argument is not a resource.
     */
    public function __construct($stream)
    {
        $this->attach($stream);
    }

    #[\Override]
    public function getMetadata(?string $key = null)
    {
        $this->meta = stream_get_meta_data($this->stream);
        if (is_null($key) === true) {
            return $this->meta;
        }

        return isset($this->meta[$key]) ? $this->meta[$key] : null;
    }

    /**
     * Is a resource attached to this stream?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    protected function isAttached()
    {
        return is_resource($this->stream);
    }

    /**
     * Attach new resource to this object.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param resource $newStream A PHP resource handle.
     *
     * @throws InvalidArgumentException If argument is not a valid PHP resource.
     */
    protected function attach($newStream)
    {
        if (is_resource($newStream) === false) {
            throw new InvalidArgumentException(__METHOD__ . ' argument must be a valid PHP resource');
        }

        if ($this->isAttached() === true) {
            $this->detach();
        }

        $this->stream = $newStream;
    }

    #[\Override]
    public function detach()
    {
        $oldResource = $this->stream;
        $this->stream = null;
        $this->meta = null;
        $this->readable = null;
        $this->writable = null;
        $this->seekable = null;
        $this->size = null;
        $this->isPipe = null;

        return $oldResource;
    }

    #[\Override]
    public function __toString(): string
    {
        if (!$this->isAttached()) {
            return '';
        }

        try {
            $this->rewind();
            return $this->getContents();
        } catch (RuntimeException $e) {
            return '';
        }
    }
    
    #[\Override]
    public function close(): void
    {
        if ($this->isAttached() === true) {
            if ($this->isPipe()) {
                pclose($this->stream);
            } else {
                fclose($this->stream);
            }
        }

        $this->detach();
    }

    #[\Override]
    public function getSize(): ?int
    {
        if (!$this->size && $this->isAttached() === true) {
            $stats = fstat($this->stream);
            $this->size = isset($stats['size']) && !$this->isPipe() ? $stats['size'] : null;
        }

        return $this->size;
    }

    #[\Override]
    public function tell(): int
    {
        if (!$this->isAttached() || ($position = ftell($this->stream)) === false || $this->isPipe()) {
            throw new RuntimeException('Could not get the position of the pointer in stream');
        }

        return $position;
    }

    #[\Override]
    public function eof(): bool
    {
        return $this->isAttached() ? feof($this->stream) : true;
    }

    #[\Override]
    public function isReadable(): bool
    {
        if ($this->readable === null) {
            if ($this->isPipe()) {
                $this->readable = true;
            } else {
                $this->readable = false;
                if ($this->isAttached()) {
                    $meta = $this->getMetadata();
                    foreach (self::$modes['readable'] as $mode) {
                        if (strpos($meta['mode'], $mode) === 0) {
                            $this->readable = true;
                            break;
                        }
                    }
                }
            }
        }

        return $this->readable;
    }

    #[\Override]
    public function isWritable(): bool
    {
        if ($this->writable === null) {
            $this->writable = false;
            if ($this->isAttached()) {
                $meta = $this->getMetadata();
                foreach (self::$modes['writable'] as $mode) {
                    if (strpos($meta['mode'], $mode) === 0) {
                        $this->writable = true;
                        break;
                    }
                }
            }
        }

        return $this->writable;
    }

    #[\Override]
    public function isSeekable(): bool
    {
        if ($this->seekable === null) {
            $this->seekable = false;
            if ($this->isAttached()) {
                $meta = $this->getMetadata();
                $this->seekable = !$this->isPipe() && $meta['seekable'];
            }
        }

        return $this->seekable;
    }

    #[\Override]
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        // Note that fseek returns 0 on success!
        if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException('Could not seek in stream');
        }
    }

    #[\Override]
    public function rewind(): void
    {
        if (!$this->isSeekable() || rewind($this->stream) === false) {
            throw new RuntimeException('Could not rewind stream');
        }
    }

    #[\Override]
    public function read(int $length): string
    {
        if (!$this->isReadable() || ($data = fread($this->stream, $length)) === false) {
            throw new RuntimeException('Could not read from stream');
        }

        return $data;
    }

    #[\Override]
    public function write(string $string): int
    {
        if (!$this->isWritable() || ($written = fwrite($this->stream, $string)) === false) {
            throw new RuntimeException('Could not write to stream');
        }

        // reset size so that it will be recalculated on next call to getSize()
        $this->size = null;

        return $written;
    }

    #[\Override]
    public function getContents(): string
    {
        if (!$this->isReadable() || ($contents = stream_get_contents($this->stream)) === false) {
            throw new RuntimeException('Could not get contents of stream');
        }

        return $contents;
    }

    /**
     * Returns whether or not the stream is a pipe.
     *
     * @return bool
     */
    private function isPipe()
    {
        if ($this->isPipe === null) {
            $this->isPipe = false;
            if ($this->isAttached()) {
                $mode = fstat($this->stream)['mode'];
                $this->isPipe = ($mode & self::FSTAT_MODE_S_IFIFO) !== 0;
            }
        }

        return $this->isPipe;
    }
}
