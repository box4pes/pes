<?php

namespace Pes\Http;

use Psr\Http\Message\ResponseInterface;

use Psr\Log\LoggerInterface;

/**
 * Description of ResponseSender
 *
 * @author pes2704
 */
class ResponseSender implements ResponseSenderInterface {

    private $logger;

    public function __construct(LoggerInterface $logger=NULL) {
        $this->logger = $logger;
    }

    /**
     * Send the response the client
     *
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response) {
        // Send response
        if (!headers_sent()) {
            // Status
            $statusHeaderContent = sprintf(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            );
            if (isset($this->logger)) {
                $this->logger->debug("ResponseSender: Send status header with content: {statusHesderContent}", ['statusHesderContent'=>$statusHeaderContent]);
            }
            header($statusHeaderContent);

            // Headers
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    $headerContent = sprintf('%s: %s', $name, $value);
                    if (isset($this->logger)) {
                        $this->logger->debug("ResponseSender: Send header with content: {headerContent}", ['headerContent'=>$headerContent]);
                    }
                    header($headerContent, false);
                }
            }
        } elseif (isset($this->logger)) {
            $this->logger->warning("ResponseSender: Some status header sent before send() method call. No any response headers was send by sender.");
        }
        //
        $this->panicOutputBufferFlush();

        // Body
        if ( !in_array($response->getStatusCode(), [204, 205, 304])) {        //ResponseStatus->isEmpty($response)
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            //TODO: Svoboda !!! dočasná úprava - chunk site natvrdo 1024*8 - nemám settings
//            $settings       = $this->container->get('settings');
//            $chunkSize      = $settings['responseChunkSize'];
            $chunkSize      = 1024*8;
            $contentLength  = $response->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }

            if (isset($this->logger)) {
                $this->logger->debug("ResponseSender: Try to send {contentLength} bytes of body.", ['contentLength'=>$contentLength ?? "unknown amount of"]);
                $this->logger->debug("ResponseSender: Will send body content in chunks with {chunk} bytes length.", ['chunk'=>$chunkSize]);
            }

            if (isset($contentLength)) {
                $amountToRead = $contentLength;
                while ($amountToRead > 0 && !$body->eof()) {
                    $lengthToSend = min($chunkSize, $amountToRead);
                    $data = $body->read($lengthToSend);
                    echo $data;

                    $amountToRead -= strlen($data);  //$body->read je "string" - jenže ve skutečnosti je to reference na stream, $body->read posune kurzor, echo vrátí příslušnou porci a strlen($data) vrací délku zbývajíciho obsah body
                    if (isset($this->logger)) {
                        $this->logger->debug("ResponseSender: Sended {len} bytes of body. The rest of body is {rest} bytes.", ['len'=>$lengthToSend, 'rest'=>$amountToRead]);
                    }
                    if ($this->connectionLost()) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read($chunkSize);
                    if (isset($this->logger)) {
                        $this->logger->debug("ResponseSender: Sended {len} bytes of body.", ['len'=>$chunkSize]);
                    }
                    if ($this->connectionLost()) {
                        break;
                    }
                }
            }
        } else {
            if (isset($this->logger)) {
                $this->logger->debug("ResponseSender: Response has no body content. Status code is : {statusCode}. Finished.", ['statusCode'=>$response->getStatusCode()]);
            }
        }
    }

    /**
     * Loops through the output buffer, flushing each, before emitting
     * the response.
     *
     */
    private function panicOutputBufferFlush()
    {
        $obLevel = ob_get_level();
        if ($obLevel) {
            $obLength = 0;
            while (ob_get_level() > 0) {
                $obLength = $obLength + ob_get_length();
                ob_end_flush();
            }
            if (isset($this->logger)) {
                $this->logger->warning("ResponseSender: Output buffering is on and some content found in output buffer. Outpu buffer level found {level} and buffer content length was {length}.",
                        ['level'=>$obLevel, 'length'=>$obLength]);
            }
        }
    }

    private function connectionLost() {
        if (connection_status() == \CONNECTION_NORMAL) {
            return FALSE;
        }
        if (isset($this->logger)) {
            $this->logger->debug("ResponseSender: PHP remote client disconnect with status {status}.", ['status'=> $this->getConnectionStatusString()]);
        }
        return TRUE;
    }

    private function getConnectionStatusString() {
        switch (connection_status()) {
            case \CONNECTION_ABORTED || \CONNECTION_TIMEOUT:
                $status = 'CONNECTION_ABORTED and CONNECTION_TIMEOUT';
                break;
            case \CONNECTION_TIMEOUT:
                $status = 'CONNECTION_TIMEOUT';
                break;
            case \CONNECTION_ABORTED:
                $status = 'CONNECTION_ABORTED';
                break;
        }
        return $status;
    }

    /**
     * https://github.com/http-interop
     */
    public function sendByInterop(ResponseInterface $response) {
        $http_line = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        header($http_line, true, $response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        $stream = $response->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }
}
