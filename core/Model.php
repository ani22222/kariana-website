<?php
namespace Core;

use PDO;

/**
 * Base PDO Model
 * Provides high-level CRUD methods with 100% prepared statement security
 */
abstract class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected ?PDO $db = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key
     */
    public function find(int|string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find a single record by a specific column
     */
    public function findBy(string $column, mixed $value): ?array
    {
        // Whitelist column name format
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }

        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :val LIMIT 1");
        $stmt->execute(['val' => $value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get records matching an associative array of column => value conditions
     */
    public function where(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                    continue;
                }
                $placeholder = 'p_' . preg_replace('/[^a-zA-Z0-9]/', '', $col);
                if ($val === null) {
                    $clauses[] = "`{$col}` IS NULL";
                } else {
                    $clauses[] = "`{$col}` = :{$placeholder}";
                    $params[$placeholder] = $val;
                }
            }
            if (!empty($clauses)) {
                $sql .= " WHERE " . implode(' AND ', $clauses);
            }
        }

        if (!empty($orderBy)) {
            $trimmedOrder = trim($orderBy);
            if (!preg_match('/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i', $trimmedOrder)) {
                throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
            }
            // Secondary per-clause structural validation to eliminate keyword stuffing
            $orderParts = explode(',', $trimmedOrder);
            foreach ($orderParts as $part) {
                $part = trim($part);
                if ($part === '' || !preg_match('/^[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)?(?:\s+(?:ASC|DESC))?$/i', $part)) {
                    throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
                }
            }
            $sql .= " ORDER BY {$trimmedOrder}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Retrieve all records
     */
    public function all(string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        if (!empty($orderBy)) {
            $trimmedOrder = trim($orderBy);
            if (!preg_match('/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i', $trimmedOrder)) {
                throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
            }
        }
        return $this->where([], $orderBy, $limit, $offset);
    }

    /**
     * Count matching records
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM `{$this->table}`";
        $params = [];

        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                    continue;
                }
                $placeholder = 'p_' . preg_replace('/[^a-zA-Z0-9]/', '', $col);
                if ($val === null) {
                    $clauses[] = "`{$col}` IS NULL";
                } else {
                    $clauses[] = "`{$col}` = :{$placeholder}";
                    $params[$placeholder] = $val;
                }
            }
            if (!empty($clauses)) {
                $sql .= " WHERE " . implode(' AND ', $clauses);
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Create a new record
     * 
     * @return int|string The last inserted ID
     */
    public function create(array $data): int|string
    {
        $cols = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $col => $val) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                continue;
            }
            $placeholder = 'ins_' . preg_replace('/[^a-zA-Z0-9]/', '', $col);
            $cols[] = "`{$col}`";
            $placeholders[] = ":{$placeholder}";
            $params[$placeholder] = $val;
        }

        $colString = implode(', ', $cols);
        $placeholderString = implode(', ', $placeholders);

        $sql = "INSERT INTO `{$this->table}` ({$colString}) VALUES ({$placeholderString})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $this->db->lastInsertId();
    }

    /**
     * Update an existing record by ID
     */
    public function update(int|string $id, array $data): bool
    {
        $setClauses = [];
        $params = ['pk_id' => $id];

        foreach ($data as $col => $val) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                continue;
            }
            $placeholder = 'upd_' . preg_replace('/[^a-zA-Z0-9]/', '', $col);
            $setClauses[] = "`{$col}` = :{$placeholder}";
            $params[$placeholder] = $val;
        }

        if (empty($setClauses)) {
            return false;
        }

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :pk_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a record by ID
     */
    public function delete(int|string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Execute a raw SQL query with prepared parameters
     */
    public function raw(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
