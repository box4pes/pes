<?php
namespace Pes\Readers\FileInfo;

/**
 *
 * @author pes2704
 */
interface FileInfoInterface {
    public function getFullFileName();
    public function getDirName();
    public function getBaseName();
    public function getFileName();
    public function getExtension();
    /**
     * Každý reader musí vracet Content-Type. Content-Type (také MIME type souboru) je dvojice řetězců oddělených lomítkem, např text/html.
     * I readery, které svá data nenačítají ze souboru musí Content-Type vracet.
     */
    public function getContentType();
}
