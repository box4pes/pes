<?php
namespace Pes\Database\Handler\AttributesProvider;

/**
 * Description of MssqlAttributesSetter
 *
 * @author pes2704
 */
final class AttributesProviderMssql extends AttributesProvider {
    /**
     *
     * @param \Pes\Database\Handler\Handler $handler Metoda využívá parametr handler
     * @return array
     */
    public function getAttributesArray(array $attributes=[]) {

//https://docs.microsoft.com/en-us/sql/connect/php/constants-microsoft-drivers-for-php-for-sql-server
//
//Encoding Constants
//
//The PDO::SQLSRV_ATTR_ENCODING attribute can be passed to PDOStatement::setAttribute, PDO::setAttribute, PDO::prepare, PDOStatement::bindColumn, and PDOStatement::bindParam.
//
//The available values to pass to PDO::SQLSRV_ATTR_ENCODING are
//PDO_SQLSRV driver constant 	Description
//PDO::SQLSRV_ENCODING_BINARY 	Data is a raw byte stream from the server without performing encoding or translation.
//
//Not valid for PDO::setAttribute.
//PDO::SQLSRV_ENCODING_SYSTEM 	Data is 8-bit characters as specified in the code page of the Windows locale that is set on the system. Any multi-byte characters or characters that do not map into this code page are substituted with a single byte question mark (?) character.
//PDO::SQLSRV_ENCODING_UTF8 	Data is in the UTF-8 encoding. This is the default encoding.
//PDO::SQLSRV_ENCODING_DEFAULT 	Uses PDO::SQLSRV_ENCODING_SYSTEM if specified during connection.
//
//Use the connection’s encoding if specified in a prepare statement.
        if (strcasecmp($this->connectionInfo->getCharset(), 'UTF8')) {
            $this->attributes[\PDO::SQLSRV_ATTR_ENCODING] = \PDO::SQLSRV_ENCODING_UTF8;  // použij výhradně kódování utf8
        }
        return parent::getAttributesArray($attributes);
    }
}
