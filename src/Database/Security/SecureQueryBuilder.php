<?php
/**
 * Secure Query Builder - Prevents SQL injection by design
 * Uses whitelist validation and proper parameter binding
 * 
 * @author Security Enhancement
 */
namespace Pes\Database\Security;

use Pes\Database\Handler\HandlerInterface;
use InvalidArgumentException;

class SecureQueryBuilder {
    
    private HandlerInterface $handler;
    private ?DatabaseWhitelistValidator $validator;
    private ?IdentifierQuoter $quoter;
    
    private array $query = [
        'select' => [],
        'from' => null,
        'where' => [],
        'orderBy' => [],
        'limit' => null,
        'offset' => null,
        'join' => [],
    ];
    
    private array $params = [];
    private int $paramCounter = 0;
    
    public function __construct(
        HandlerInterface $handler,
        ?DatabaseWhitelistValidator $validator = null,
        ?IdentifierQuoter $quoter = null
    ) {
        $this->handler = $handler;
        $this->validator = $validator;
        $this->quoter = $quoter ?? new IdentifierQuoter($handler);
    }
    
    public function select(string ...$columns): self {
        foreach ($columns as $column) {
            $this->query['select'][] = $this->quoteColumn($column);
        }
        return $this;
    }
    
    public function selectAll(): self {
        $this->query['select'][] = '*';
        return $this;
    }
    
    public function from(string $table): self {
        $validatedTable = $this->validateAndQuoteTable($table);
        $this->query['from'] = $validatedTable;
        return $this;
    }
    
    public function join(string $table, string $onCondition): self {
        $validatedTable = $this->validateAndQuoteTable($table);
        $this->query['join'][] = "INNER JOIN $validatedTable ON $onCondition";
        return $this;
    }
    
    public function leftJoin(string $table, string $onCondition): self {
        $validatedTable = $this->validateAndQuoteTable($table);
        $this->query['join'][] = "LEFT JOIN $validatedTable ON $onCondition";
        return $this;
    }
    
    public function where(string $column, mixed $value): self {
        return $this->whereCondition($column, '=', $value);
    }
    
    public function whereNot(string $column, mixed $value): self {
        return $this->whereCondition($column, '!=', $value);
    }
    
    public function whereLike(string $column, mixed $value): self {
        return $this->whereCondition($column, 'LIKE', $value);
    }
    
    public function whereIn(string $column, array $values): self {
        $quotedColumn = $this->quoteColumn($column);
        $placeholders = [];
        
        foreach ($values as $value) {
            $placeholder = $this->generatePlaceholder();
            $placeholders[] = $placeholder;
            $this->params[$placeholder] = $value;
        }
        
        $this->query['where'][] = "$quotedColumn IN (" . implode(', ', $placeholders) . ")";
        return $this;
    }
    
    public function whereNull(string $column): self {
        $quotedColumn = $this->quoteColumn($column);
        $this->query['where'][] = "$quotedColumn IS NULL";
        return $this;
    }
    
    public function whereNotNull(string $column): self {
        $quotedColumn = $this->quoteColumn($column);
        $this->query['where'][] = "$quotedColumn IS NOT NULL";
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException("Invalid order direction: $direction");
        }
        
        $quotedColumn = $this->quoteColumn($column);
        $this->query['orderBy'][] = "$quotedColumn $direction";
        return $this;
    }
    
    public function limit(int $limit): self {
        if ($limit < 0) {
            throw new InvalidArgumentException("Limit must be non-negative");
        }
        $this->query['limit'] = $limit;
        return $this;
    }
    
    public function offset(int $offset): self {
        if ($offset < 0) {
            throw new InvalidArgumentException("Offset must be non-negative");
        }
        $this->query['offset'] = $offset;
        return $this;
    }
    
    public function fetchAll(): array {
        [$sql, $params] = $this->build();
        
        $stmt = $this->handler->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function fetchOne(): ?array {
        $this->limit(1);
        [$sql, $params] = $this->build();
        
        $stmt = $this->handler->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }
    
    public function build(): array {
        $sqlParts = [];
        
        if (empty($this->query['select'])) {
            $sqlParts[] = 'SELECT *';
        } else {
            $sqlParts[] = 'SELECT ' . implode(', ', $this->query['select']);
        }
        
        if ($this->query['from'] === null) {
            throw new InvalidArgumentException("FROM clause is required");
        }
        $sqlParts[] = 'FROM ' . $this->query['from'];
        
        foreach ($this->query['join'] as $join) {
            $sqlParts[] = $join;
        }
        
        if (!empty($this->query['where'])) {
            $sqlParts[] = 'WHERE ' . implode(' AND ', $this->query['where']);
        }
        
        if (!empty($this->query['orderBy'])) {
            $sqlParts[] = 'ORDER BY ' . implode(', ', $this->query['orderBy']);
        }
        
        if ($this->query['limit'] !== null) {
            $sqlParts[] = 'LIMIT ' . $this->query['limit'];
            if ($this->query['offset'] !== null) {
                $sqlParts[] = 'OFFSET ' . $this->query['offset'];
            }
        }
        
        return [implode(' ', $sqlParts), $this->params];
    }
    
    public function reset(): self {
        $this->query = [
            'select' => [],
            'from' => null,
            'where' => [],
            'orderBy' => [],
            'limit' => null,
            'offset' => null,
            'join' => [],
        ];
        $this->params = [];
        $this->paramCounter = 0;
        return $this;
    }
    
    private function validateAndQuoteTable(string $table): string {
        if ($this->validator !== null) {
            if (!$this->validator->validateTable($table)) {
                throw new InvalidArgumentException("Table '$table' is not in whitelist");
            }
        }
        
        return $this->quoter->quoteTable($table);
    }
    
    private function quoteColumn(string $column): string {
        if (str_contains($column, '.')) {
            $parts = explode('.', $column);
            return implode('.', array_map(
                fn($part) => $this->quoter->quoteColumn(trim($part)),
                $parts
            ));
        }
        
        if ($this->validator !== null) {
            if (!$this->validator->validateColumnFormat($column)) {
                throw new InvalidArgumentException("Invalid column name format: $column");
            }
        }
        
        return $this->quoter->quoteColumn($column);
    }
    
    private function whereCondition(string $column, string $operator, mixed $value): self {
        $quotedColumn = $this->quoteColumn($column);
        $placeholder = $this->generatePlaceholder();
        
        $this->query['where'][] = "$quotedColumn $operator $placeholder";
        $this->params[$placeholder] = $value;
        
        return $this;
    }
    
    private function generatePlaceholder(): string {
        return ':p' . (++$this->paramCounter);
    }
}
