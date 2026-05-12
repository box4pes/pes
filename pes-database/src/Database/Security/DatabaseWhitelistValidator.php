<?php
/**
 * Whitelist validator for database objects (tables, columns)
 * Prevents SQL injection by validating against known safe identifiers
 * 
 * @author Security Enhancement
 */
namespace Pes\Database\Security;

use Pes\Database\Metadata\MetadataProviderInterface;
use Pes\Database\Metadata\TableMetadataInterface;

class DatabaseWhitelistValidator {
    
    private $metadataProvider;
    private $cache = [];
    
    // Allowed regex patterns for identifiers
    private const ALLOWED_TABLE_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    private const ALLOWED_COLUMN_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    
    public function __construct(MetadataProviderInterface $metadataProvider) {
        $this->metadataProvider = $metadataProvider;
    }
    
    /**
     * Validate table name against whitelist
     * 
     * @param string $tableName Table name to validate
     * @return bool True if table is valid
     * @throws \InvalidArgumentException If table name is invalid
     */
    public function validateTable(string $tableName): bool {
        // Check format first
        if (!preg_match(self::ALLOWED_TABLE_PATTERN, $tableName)) {
            throw new \InvalidArgumentException("Invalid table name format: {$tableName}");
        }
        
        // Check cache first
        if (isset($this->cache['tables'][$tableName])) {
            return $this->cache['tables'][$tableName];
        }
        
        try {
            $metadata = $this->metadataProvider->getTableMetadata($tableName);
            $isValid = $metadata instanceof TableMetadataInterface;
            $this->cache['tables'][$tableName] = $isValid;
            return $isValid;
        } catch (\Exception $e) {
            $this->cache['tables'][$tableName] = false;
            throw new \InvalidArgumentException("Table '{$tableName}' does not exist or is not accessible");
        }
    }
    
    /**
     * Validate column name against whitelist for a specific table
     * 
     * @param string $tableName Table name
     * @param string $columnName Column name to validate
     * @return bool True if column is valid for the table
     * @throws \InvalidArgumentException If column name is invalid
     */
    public function validateColumn(string $tableName, string $columnName): bool {
        // Validate table first
        $this->validateTable($tableName);
        
        // Check format first
        if (!preg_match(self::ALLOWED_COLUMN_PATTERN, $columnName)) {
            throw new \InvalidArgumentException("Invalid column name format: {$columnName}");
        }
        
        // Check cache
        $cacheKey = "{$tableName}.{$columnName}";
        if (isset($this->cache['columns'][$cacheKey])) {
            return $this->cache['columns'][$cacheKey];
        }
        
        try {
            $tableMetadata = $this->metadataProvider->getTableMetadata($tableName);
            $isValid = $tableMetadata->getColumnMetadata($columnName) !== null;
            $this->cache['columns'][$cacheKey] = $isValid;
            
            if (!$isValid) {
                throw new \InvalidArgumentException("Column '{$columnName}' does not exist in table '{$tableName}'");
            }
            
            return true;
        } catch (\Exception $e) {
            $this->cache['columns'][$cacheKey] = false;
            throw $e;
        }
    }
    
    /**
     * Validate multiple column names for a table
     * 
     * @param string $tableName Table name
     * @param array $columnNames Array of column names to validate
     * @return array Array of valid column names
     * @throws \InvalidArgumentException If any column is invalid
     */
    public function validateColumns(string $tableName, array $columnNames): array {
        $validColumns = [];
        foreach ($columnNames as $columnName) {
            $this->validateColumn($tableName, $columnName);
            $validColumns[] = $columnName;
        }
        return $validColumns;
    }
    
    /**
     * Get all valid table names from the database
     * 
     * @return array Array of valid table names
     */
    public function getValidTables(): array {
        if (!isset($this->cache['all_tables'])) {
            try {
                // This requires extending MetadataProvider to support getAllTables
                if (method_exists($this->metadataProvider, 'getAllTablesMetadata')) {
                    $metadata = $this->metadataProvider->getAllTablesMetadata();
                    $this->cache['all_tables'] = array_keys($metadata);
                } else {
                    // Fallback: use information schema
                    $this->cache['all_tables'] = $this->fetchAllTables();
                }
            } catch (\Exception $e) {
                throw new \RuntimeException("Failed to retrieve table list: " . $e->getMessage());
            }
        }
        return $this->cache['all_tables'];
    }
    
    /**
     * Get all valid column names for a table
     * 
     * @param string $tableName Table name
     * @return array Array of valid column names
     * @throws \InvalidArgumentException If table is invalid
     */
    public function getValidColumns(string $tableName): array {
        $this->validateTable($tableName);
        
        $cacheKey = "columns_{$tableName}";
        if (!isset($this->cache[$cacheKey])) {
            try {
                $tableMetadata = $this->metadataProvider->getTableMetadata($tableName);
                $this->cache[$cacheKey] = array_keys($tableMetadata->getColumnsMetadata());
            } catch (\Exception $e) {
                throw new \RuntimeException("Failed to retrieve column list for table '{$tableName}': " . $e->getMessage());
            }
        }
        return $this->cache[$cacheKey];
    }
    
    /**
     * Clear the validation cache
     */
    public function clearCache(): void {
        $this->cache = [];
    }
    
    /**
     * Fallback method to fetch all tables directly from database
     * 
     * @return array Array of table names
     */
    private function fetchAllTables(): array {
        try {
            // This would need access to the PDO handler
            // For now, return empty array - should be implemented based on specific needs
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
}