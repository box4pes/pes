<?php
/**
 * Extended Whitelist Validator with additional validation methods
 * 
 * @author Security Enhancement
 */
namespace Pes\Database\Security;

use Pes\Database\Metadata\MetadataProviderInterface;

class DatabaseWhitelistValidatorExtension extends DatabaseWhitelistValidator {
    
    /**
     * Validate column name format only (without database check)
     * Useful when you just need quick format validation
     * 
     * @param string $columnName Column name to validate
     * @return bool True if format is valid
     * @throws \InvalidArgumentException If column name format is invalid
     */
    public function validateColumnFormat(string $columnName): bool {
        if (!preg_match(self::ALLOWED_COLUMN_PATTERN, $columnName)) {
            throw new \InvalidArgumentException("Invalid column name format: {$columnName}");
        }
        return true;
    }
    
    /**
     * Validate table name format only (without database check)
     * Useful when you just need quick format validation
     * 
     * @param string $tableName Table name to validate
     * @return bool True if format is valid
     * @throws \InvalidArgumentException If table name format is invalid
     */
    public function validateTableFormat(string $tableName): bool {
        if (!preg_match(self::ALLOWED_TABLE_PATTERN, $tableName)) {
            throw new \InvalidArgumentException("Invalid table name format: {$tableName}");
        }
        return true;
    }
    
    /**
     * Static method to validate format without instance
     * 
     * @param string $identifier Table or column name
     * @return bool
     */
    public static function isValidIdentifierFormat(string $identifier): bool {
        return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier);
    }
}
