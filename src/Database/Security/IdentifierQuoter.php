<?php
/**
 * Automatic identifier quoter for SQL security
 * Properly escapes and quotes database identifiers to prevent SQL injection
 * 
 * @author Security Enhancement
 */
namespace Pes\Database\Security;

use Pes\Database\Handler\HandlerInterface;

class IdentifierQuoter {
    
    private $handler;
    private $quoteCharacter;
    
    public function __construct(HandlerInterface $handler) {
        $this->handler = $handler;
        $this->determineQuoteCharacter();
    }
    
    /**
     * Quote a table name
     * 
     * @param string $tableName Table name to quote
     * @return string Quoted table name
     */
    public function quoteTable(string $tableName): string {
        return $this->quoteIdentifier($tableName);
    }
    
    /**
     * Quote a column name
     * 
     * @param string $columnName Column name to quote
     * @return string Quoted column name
     */
    public function quoteColumn(string $columnName): string {
        return $this->quoteIdentifier($columnName);
    }
    
    /**
     * Quote a table.column combination
     * 
     * @param string $tableName Table name
     * @param string $columnName Column name
     * @return string Quoted table.column
     */
    public function quoteTableColumn(string $tableName, string $columnName): string {
        return $this->quoteIdentifier($tableName) . '.' . $this->quoteIdentifier($columnName);
    }
    
    /**
     * Quote multiple column names
     * 
     * @param array $columnNames Array of column names
     * @return array Array of quoted column names
     */
    public function quoteColumns(array $columnNames): array {
        return array_map([$this, 'quoteColumn'], $columnNames);
    }
    
    /**
     * Quote multiple table names
     * 
     * @param array $tableNames Array of table names
     * @return array Array of quoted table names
     */
    public function quoteTables(array $tableNames): array {
        return array_map([$this, 'quoteTable'], $tableNames);
    }
    
    /**
     * Quote an identifier (table or column name)
     * 
     * @param string $identifier Identifier to quote
     * @return string Quoted identifier
     */
    private function quoteIdentifier(string $identifier): string {
        // Handle nested identifiers like database.table.column
        $parts = explode('.', $identifier);
        $quotedParts = array_map([$this, 'quoteSingleIdentifier'], $parts);
        return implode('.', $quotedParts);
    }
    
    /**
     * Quote a single identifier part
     * 
     * @param string $identifier Single identifier part
     * @return string Quoted identifier
     */
    private function quoteSingleIdentifier(string $identifier): string {
        // Remove any existing quotes to prevent double-quoting
        $identifier = trim($identifier, $this->quoteCharacter);
        
        // Validate identifier format
        if (!$this->isValidIdentifier($identifier)) {
            throw new \InvalidArgumentException("Invalid identifier format: {$identifier}");
        }
        
        return $this->quoteCharacter . $identifier . $this->quoteCharacter;
    }
    
    /**
     * Validate if identifier is safe to quote
     * 
     * @param string $identifier Identifier to validate
     * @return bool True if identifier is valid
     */
    private function isValidIdentifier(string $identifier): bool {
        // Allow alphanumeric characters and underscores, but not empty
        // Also prevent semicolons and other SQL injection attempts
        $pattern = '/^[a-zA-Z0-9_]+$/';
        
        if (empty($identifier)) {
            return false;
        }
        
        if (!preg_match($pattern, $identifier)) {
            return false;
        }
        
        // Check for SQL keywords (basic protection)
        $sqlKeywords = [
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE', 'ALTER',
            'UNION', 'WHERE', 'JOIN', 'INNER', 'OUTER', 'LEFT', 'RIGHT',
            'GROUP', 'BY', 'ORDER', 'HAVING', 'LIMIT', 'OFFSET'
        ];
        
        $upperIdentifier = strtoupper($identifier);
        foreach ($sqlKeywords as $keyword) {
            if ($upperIdentifier === $keyword) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Determine the appropriate quote character based on database type
     */
    private function determineQuoteCharacter(): void {
        try {
            $driverName = $this->handler->getAttribute(\PDO::ATTR_DRIVER_NAME);
            
            switch (strtolower($driverName)) {
                case 'mysql':
                    $this->quoteCharacter = '`';
                    break;
                case 'pgsql':
                case 'sqlite':
                case 'sqlsrv':
                case 'oci':
                default:
                    $this->quoteCharacter = '"';
                    break;
            }
        } catch (\Exception $e) {
            // Default to double quotes if we can't determine the driver
            $this->quoteCharacter = '"';
        }
    }
    
    /**
     * Get the current quote character
     * 
     * @return string Quote character being used
     */
    public function getQuoteCharacter(): string {
        return $this->quoteCharacter;
    }
    
    /**
     * Build a safe SELECT clause with quoted identifiers
     * 
     * @param array $columns Array of column names (or ['*'] for all)
     * @param string $table Table name
     * @return string Safe SELECT clause
     */
    public function buildSelectClause(array $columns, string $table): string {
        if (empty($columns) || (count($columns) === 1 && $columns[0] === '*')) {
            return 'SELECT * FROM ' . $this->quoteTable($table);
        }
        
        $quotedColumns = $this->quoteColumns($columns);
        $columnList = implode(', ', $quotedColumns);
        $quotedTable = $this->quoteTable($table);
        
        return "SELECT {$columnList} FROM {$quotedTable}";
    }
    
    /**
     * Build a safe WHERE clause with quoted identifiers
     * 
     * @param array $conditions Associative array of column => value pairs
     * @param string $table Table name (for validation)
     * @return string Safe WHERE clause with placeholders
     */
    public function buildWhereClause(array $conditions, string $table): string {
        if (empty($conditions)) {
            return '';
        }
        
        $whereParts = [];
        foreach ($conditions as $column => $value) {
            $quotedColumn = $this->quoteTableColumn($table, $column);
            $placeholder = ':' . $column;
            $whereParts[] = "{$quotedColumn} = {$placeholder}";
        }
        
        return 'WHERE ' . implode(' AND ', $whereParts);
    }
    
    /**
     * Build a safe ORDER BY clause with quoted identifiers
     * 
     * @param array $orderBy Array of [column => direction] pairs
     * @param string $table Table name
     * @return string Safe ORDER BY clause
     */
    public function buildOrderByClause(array $orderBy, string $table): string {
        if (empty($orderBy)) {
            return '';
        }
        
        $orderParts = [];
        foreach ($orderBy as $column => $direction) {
            $quotedColumn = $this->quoteTableColumn($table, $column);
            $direction = strtoupper(trim($direction));
            if (!in_array($direction, ['ASC', 'DESC'])) {
                $direction = 'ASC';
            }
            $orderParts[] = "{$quotedColumn} {$direction}";
        }
        
        return 'ORDER BY ' . implode(', ', $orderParts);
    }
}