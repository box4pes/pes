<?php
/**
 * SafeManipulator - Secure database operations
 * Extends Manipulator with SQL injection protection
 * 
 * @author Security Enhancement
 */
namespace Pes\Database\Security;

use Pes\Database\Handler\HandlerInterface;
use Pes\Database\Manipulator\Manipulator;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class SafeManipulator extends Manipulator {
    
    private ?DatabaseWhitelistValidator $validator;
    private IdentifierQuoter $quoter;
    
    public function __construct(
        HandlerInterface $handler,
        LoggerInterface $logger,
        ?DatabaseWhitelistValidator $validator = null
    ) {
        parent::__construct($handler, $logger);
        $this->validator = $validator;
        $this->quoter = new IdentifierQuoter($handler);
    }
    
    /**
     * SECURE version of findAllRows - validates table name
     * 
     * @param string $tablename
     * @return array
     */
    public function findAllRows(string $tablename): array {
        $validatedTable = $this->validateTable($tablename);
        
        $query = "SELECT * FROM $validatedTable";
        
        $stmt = $this->getHandler()->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * SECURE version of find - validates table and uses parameterized queries
     * 
     * @param string $tablename
     * @param array $criteriaArray
     * @param array $orderBy
     * @param int|null $limit
     * @return array
     */
    public function find(
        string $tablename, 
        array $criteriaArray = [], 
        array $orderBy = [], 
        ?int $limit = null
    ): array {
        $validatedTable = $this->validateTable($tablename);
        
        $query = "SELECT * FROM $validatedTable";
        $params = [];
        
        if (!empty($criteriaArray)) {
            $whereConditions = [];
            foreach ($criteriaArray as $key => $value) {
                $quotedColumn = $this->quoteColumn($key);
                $placeholder = ":$key";
                $whereConditions[] = "$quotedColumn = $placeholder";
                $params[$placeholder] = $value;
            }
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }
        
        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $quotedColumn = $this->quoteColumn($column);
                $direction = strtoupper($direction);
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    $direction = 'ASC';
                }
                $orderClauses[] = "$quotedColumn $direction";
            }
            $query .= " ORDER BY " . implode(", ", $orderClauses);
        }
        
        if ($limit !== null) {
            if ($limit < 0) {
                throw new InvalidArgumentException("Limit must be non-negative");
            }
            $query .= " LIMIT $limit";
        }
        
        $stmt = $this->getHandler()->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * SECURE version of copyTable - validates table names
     * 
     * @param string $sourceTableName
     * @param string $targetTableName
     * @return bool
     */
    public function copyTable(string $sourceTableName, string $targetTableName): bool {
        $validatedSource = $this->validateTable($sourceTableName);
        $validatedTarget = $this->validateTable($targetTableName);
        
        $dbhTransact = $this->getHandler();
        
        if ($this->tableExists($sourceTableName)) {
            throw new \UnexpectedValueException("Source table '$sourceTableName' does not exist.");
        }
        if ($this->tableExists($targetTableName)) {
            throw new \LogicException("Target table '$targetTableName' already exists.");
        }
        
        try {
            $dbhTransact->beginTransaction();
            $dbhTransact->exec("CREATE TABLE $validatedTarget LIKE $validatedSource");
            $dbhTransact->exec("INSERT $validatedTarget SELECT * FROM $validatedSource");
            $succ = $dbhTransact->commit();
        } catch(\PDOException $e) {
            $dbhTransact->rollBack();
            throw new \Pes\Database\Manipulator\Exception\ErrorRollbackException($e->getMessage(), 0, $e);
        }
        
        return $succ ? true : false;
    }
    
    /**
     * SECURE insert - validates table and quotes columns
     * 
     * @param string $tablename
     * @param array $data Associative array [column => value]
     * @return bool
     */
    public function insert(string $tablename, array $data): bool {
        if (empty($data)) {
            throw new InvalidArgumentException("Data array cannot be empty");
        }
        
        $validatedTable = $this->validateTable($tablename);
        
        $columns = [];
        $placeholders = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $quotedColumn = $this->quoteColumn($column);
            $placeholder = ":" . preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
            $columns[] = $quotedColumn;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $value;
        }
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $validatedTable,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->getHandler()->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * SECURE update - validates table, quotes columns, uses parameterized queries
     * 
     * @param string $tablename
     * @param array $data Associative array [column => value]
     * @param array $criteria WHERE conditions [column => value]
     * @return int Number of affected rows
     */
    public function update(string $tablename, array $data, array $criteria): int {
        if (empty($data)) {
            throw new InvalidArgumentException("Data array cannot be empty");
        }
        if (empty($criteria)) {
            throw new InvalidArgumentException("Criteria cannot be empty for UPDATE");
        }
        
        $validatedTable = $this->validateTable($tablename);
        
        $setClauses = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $quotedColumn = $this->quoteColumn($column);
            $placeholder = ":set_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
            $setClauses[] = "$quotedColumn = $placeholder";
            $params[$placeholder] = $value;
        }
        
        $whereConditions = [];
        foreach ($criteria as $column => $value) {
            $quotedColumn = $this->quoteColumn($column);
            $placeholder = ":where_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
            $whereConditions[] = "$quotedColumn = $placeholder";
            $params[$placeholder] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $validatedTable,
            implode(', ', $setClauses),
            implode(' AND ', $whereConditions)
        );
        
        $stmt = $this->getHandler()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }
    
    /**
     * SECURE delete - validates table, uses parameterized queries
     * 
     * @param string $tablename
     * @param array $criteria WHERE conditions [column => value]
     * @return int Number of affected rows
     */
    public function delete(string $tablename, array $criteria): int {
        if (empty($criteria)) {
            throw new InvalidArgumentException("Criteria cannot be empty for DELETE");
        }
        
        $validatedTable = $this->validateTable($tablename);
        
        $whereConditions = [];
        $params = [];
        
        foreach ($criteria as $column => $value) {
            $quotedColumn = $this->
