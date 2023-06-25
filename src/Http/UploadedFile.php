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
     * @param string|resource|StreamInterface $stream
     * @param int $size
     * @param int $this->error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @throws InvalidArgumentException
     */
    public function __construct(StreamInterface $stream, $size, $uploadErrorCode, $clientFilename = null, $clientMediaType = null)
    {
        $this->stream = $stream;        
        $this->size = $size;
        $this->error = $uploadErrorCode;
        $this->clientFilename = $clientFilename;
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
        if (empty($sapi) OR 0 === strpos($sapi, 'cli')) {   //http://php.net/manual/en/function.php-sapi-name.php#89858, https://stackoverflow.com/questions/10886539/why-does-php-sapi-not-equal-cli-when-called-from-a-cron-job
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
