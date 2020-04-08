<?php


namespace Pes\Http;

use Pes\Http\Exception\InvalidArgumentException;
use Pes\Http\Exception\RuntimeException;
use Pes\Http\Exception\UploadException;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var string
     */
    private $clientFilename;

    /**
     * @var string
     */
    private $clientMediaType;

    /**
     * @var int
     */
    private $error;

    /**
     * @var null|string
     */
    private $file;

    /**
     * @var bool
     */
    private $moved = false;

    /**
     * @var int
     */
    private $size;

    /**
     * @var null|StreamInterface
     */
    private $stream;

    /**
     * @param string|resource|StreamInterface $stringOrResourceOrStream
     * @param int $size
     * @param int $this->error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @throws InvalidArgumentException
     */
    public function __construct($stringOrResourceOrStream, $size, $uploadErrorCode, $clientFilename = null, $clientMediaType = null)
    {
        $this->error = $uploadErrorCode;
        if ($this->error === UPLOAD_ERR_OK) {
            if (is_string($stringOrResourceOrStream)) {
                $this->file = $stringOrResourceOrStream;
            } elseif (is_resource($stringOrResourceOrStream)) {
                $this->stream = new Stream($stringOrResourceOrStream);
            } elseif ($stringOrResourceOrStream instanceof StreamInterface) {
                $this->stream = $stringOrResourceOrStream;
            } else {
                throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
            }
        }

        if (! is_int($size)) {
            throw new InvalidArgumentException('Invalid size provided for UploadedFile; must be an int');
        }
        $this->size = $size;

        if (! is_int($this->error)
            || 0 > $this->error
            || 8 < $this->error
        ) {
            throw new InvalidArgumentException('Invalid upload error status for UploadedFile; must be some of UPLOAD_ERR_* constants');
        }
        $this->error = (new UploadedFileErrorEnum())($this->error);

        if (null !== $clientFilename && ! is_string($clientFilename)) {
            throw new InvalidArgumentException('Invalid client filename provided for UploadedFile; must be null or a string');
        }
        $this->clientFilename = $clientFilename;

        if (null !== $clientMediaType && ! is_string($clientMediaType)) {
            throw new InvalidArgumentException('Invalid client media type provided for UploadedFile; must be null or a string');
        }
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException if the upload was not successful.
     */
    public function getStream()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new UploadException('Cannot retrieve stream due to upload error', $this->error);
        }

        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        $this->stream = new Stream($this->file);
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @throws RuntimeException
     * @throws UploadException
     * @throws InvalidArgumentException
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new RuntimeException('Cannot move file; already moved!');
        }

        if ($this->error !== UPLOAD_ERR_OK) {
            throw new UploadException('Cannot retrieve stream due to upload error', $this->error);
        }

        if (! is_string($targetPath) || empty($targetPath)) {
            throw new InvalidArgumentException(
                'Invalid path provided for move operation; must be a non-empty string'
            );
        }

        $targetDirectory = dirname($targetPath);
        if (! is_dir($targetDirectory) || ! is_writable($targetDirectory)) {
            throw new RuntimeException(sprintf(
                'The target directory `%s` does not exists or is not writable',
                $targetDirectory
            ));
        }

        // PHP_SAPI - lowercase string that describes the type of interface (the Server API, SAPI) that PHP is using. For example,
        // in CLI PHP this string will be "cli" whereas with Apache it may have several different values depending on the exact SAPI used.
        $sapi = PHP_SAPI;
        if (empty($sapi) OR 0 === strpos($sapi, 'cli') OR ! $this->file) {   //http://php.net/manual/en/function.php-sapi-name.php#89858, https://stackoverflow.com/questions/10886539/why-does-php-sapi-not-equal-cli-when-called-from-a-cron-job
            // PHP spuštěno neznámým způsobem nebo přes CLI mebo neznám název souboru
            $this->writeStreamContentIntoFile($targetPath);
        } else {
            // PHP spuštěno přes SAPI
            if (false === move_uploaded_file($this->file, $targetPath)) {
                throw new RuntimeException('Error occurred while moving uploaded file');     //           error_get_last()
            }
        }

        $this->moved = true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * Write internal stream to given path
     *
     * @param string $path
     */
    private function writeStreamContentIntoFile($path)
    {
        $handle = fopen($path, 'wb+');
        if (false === $handle) {
            throw new RuntimeException('Unable to write to designated path');
        }

        $stream = $this->getStream();
        $stream->rewind();
        while (! $stream->eof()) {
            fwrite($handle, $stream->read(4096));
        }

        fclose($handle);
    }
}
