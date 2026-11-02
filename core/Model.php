<?php
namespace Core;

/**
 * Parent Model
 * 
 * Provides automated CRUD (Create, Read, Update, Delete) operations and query helper methods for child models.
 */
// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Model extends Database {

    protected $table = ""; // Target database table name defined by child classes
    protected $allowedColumns = []; // Whitelist of columns permitted during insert and update operations

    // Protected property to hold a temporary table override
    protected $tempTable = null;

    /**
     * Dynamically switch tables on the fly
     * Usage: (new User())->table('activity_logs')->insert([...]);
     */
    public function table(string $tableName) {
        // Clone the model instance so it doesn't corrupt the original model's table state
        $newModel = clone $this;
        $newModel->tempTable = $tableName;
        return $newModel;
    }

    /**
     * Helper to resolve which table to use in queries
     */
    protected function getTable() {
        // FIXED: Returns tempTable if set, otherwise falls back to the class's default table property
        return $this->tempTable ?? $this->table;
    }
    
    /**
     * Retrieve records matching specified positive and negative conditions, with optional sorting and pagination.
     *
     * @param array $data Associative array of column-value pairs for equality matching
     * @param array $data_not Associative array of column-value pairs for inequality matching
     * @param string $extra_clause Optional raw SQL constraints
     * @param string $order_by Column name to sort the result set by
     * @param string $order_dir Sort direction ('ASC' or 'DESC')
     * @param int|null $limit Maximum number of records to return
     * @param int|null $offset Number of records to skip
     * @return array|bool Result set rows as objects, or false on failure
     */
    public function where(
        array $data, 
        array $data_not = [], 
        string $extra_clause = "", 
        string $order_by = "", 
        string $order_dir = "DESC", 
        ?int $limit = null, 
        ?int $offset = null
    ) {
        $query = "SELECT * FROM " . $this->getTable() . " WHERE ";
        $params = [];

        // Build positive equality conditions.
        foreach ($data as $key => $val) {
            $query .= $key . " = :" . $key . " AND ";
            $params[$key] = $val;
        }

        // Build negative inequality conditions with prefixed placeholders to avoid collisions.
        foreach ($data_not as $key => $val) {
            $not_key = "not_" . $key;
            $query .= $key . " != :" . $not_key . " AND ";
            $params[$not_key] = $val;
        }

        // Append custom raw SQL constraints if provided.
        if (!empty($extra_clause)) {
            $query .= "(" . $extra_clause . ") AND ";
        }

        // Clean up trailing operators from the query string.
        $query = rtrim($query, " AND ");
        if (substr($query, -7) === " WHERE ") {
            $query = substr($query, 0, -7);
        }

        // Append ordering clauses.
        if (!empty($order_by)) {
            $direction = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';
            $query .= " ORDER BY " . $order_by . " " . $direction;
        }

        // Append limit and offset constraints for pagination.
        if ($limit !== null) {
            $query .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $query .= " OFFSET " . (int)$offset;
            }
        }

        return $this->query($query, $params);
    }

    /**
     * Retrieve the first record matching specified criteria.
     *
     * @param array $data Positive matching criteria
     * @param array $data_not Negative matching criteria
     * @return object|false First matching record object or false if not found
     */
    public function first($data, $data_not = []) {
        $result = $this->where($data, $data_not);
        
        // Return the first element if results exist.
        if (is_array($result) && !empty($result)) {
            return $result[0];
        }
        
        return false;
    }

    /**
     * Fetch a single record matching multiple key-value conditions with a limit of 1.
     *
     * @param array $where Associative array of conditions
     * @return object|false Result record object or false on failure
     */
    public function firstWhere($where) {
        $sql = "SELECT * FROM " . $this->getTable() . " WHERE ";
        $params = [];
        
        $conditions = [];
        foreach ($where as $column => $value) {
            $conditions[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }
        
        $sql .= implode(" AND ", $conditions) . " LIMIT 1";
        $result = $this->query($sql, $params);
        
        return $result[0] ?? false;
    }


    /**
     * UPSERT (Insert or Update on Duplicate Key)
     * Handles inserting data and automatically updates specified columns if a unique/primary key conflict occurs.
     * 
     * @param array $data Data to insert
     * @param array $updateData Columns to update on duplicate key conflict [column => value]
     * @return bool|array
     */
    public function upsert(array $data, array $updateData = []) {
        // 1. Security check against allowed columns
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "INSERT INTO " . $this->getTable() . " (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")";
        
        $params = $data;

        // 2. Handle ON DUPLICATE KEY UPDATE clauses securely with a prefix to prevent collisions
        if (!empty($updateData)) {
            $updateClauses = [];
            foreach ($updateData as $key => $value) {
                $updateClauses[] = "{$key} = :up_{$key}";
                $params["up_{$key}"] = $value;
            }
            $query .= " ON DUPLICATE KEY UPDATE " . implode(", ", $updateClauses);
        }

        return $this->query($query, $params);
    }

    /**
     * Insert a new record into the table, filtering fields against allowed columns.
     *
     * @param array $data Associative array of column-value pairs to insert
     * @return bool Returns true on success or false on failure
     */
    public function insert($data) {
        // Filter out unpermitted columns based on the whitelist security policy.
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }

        $keys = array_keys($data);
        $query = "INSERT INTO " . $this->getTable() . " (" . implode(",", $keys) . ") VALUES (:" . implode(",:", $keys) . ")";
        
        return $this->query($query, $data);
    }

    /**
     * Retrieve rows from the table as a key-value pair associative array.
     *
     * @param string $key_column Column to use for array keys
     * @param string $value_column Column to use for array values
     * @param array $where Optional filtering conditions
     * @return array Associative array of [key => value] pairs
     */
    public function keyPair(string $key_column, string $value_column, array $where = []) {
        $query = "SELECT {$key_column}, {$value_column} FROM " . $this->getTable();
        $params = [];

        // Build WHERE clause dynamically if constraints are supplied.
        if (!empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $val) {
                $query .= $key . " = :" . $key . " AND ";
                $params[$key] = $val;
            }
            $query = rtrim($query, " AND ");
        }

        return parent::queryKeyPair($query, $params);
    }

    /**
     * Update existing records matching specified conditions.
     *
     * @param mixed $where Criteria for matching rows (array of conditions or single ID value)
     * @param array $data Data fields to update
     * @param string $id_column Primary key column name for fallback single-ID lookup
     * @return bool Returns true on success or false on failure
     */
    public function update($where, $data, $id_column = 'id') {
        // Filter update fields against allowed columns.
        if (!empty($this->allowedColumns)) {
            foreach ($data as $key => $value) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                } 
            }
        }

        $query = "UPDATE " . $this->getTable() . " SET ";
        $setClauses = [];
        $bindData = [];

        // Build SET expressions with prefixed parameters to prevent name collisions.
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :set_{$key}";
            $bindData["set_{$key}"] = $value;
        }
        $query .= implode(", ", $setClauses);

        // Build WHERE clauses dynamically.
        $whereClauses = [];
        if (is_array($where)) {
            // Handle multi-condition lookup arrays.
            foreach ($where as $key => $value) {
                $whereClauses[] = "{$key} = :where_{$key}";
                $bindData["where_{$key}"] = $value;
            }
        } else {
            // Handle single identifier value lookups.
            $whereClauses[] = "{$id_column} = :where_id_col";
            $bindData["where_id_col"] = $where;
        }

        $query .= " WHERE " . implode(" AND ", $whereClauses) . " LIMIT 1";

        return $this->query($query, $bindData);
    }

    /**
     * Delete records using custom SQL conditions and explicit parameters.
     * Useful for bulk deletions, garbage collection, and time-based purging.
     * 
     * @param string $customSql SQL condition string (e.g., "last_activity < :old")
     * @param array $params Associative array of bind parameters
     * @return bool|array
     */
    public function deleteWhere(string $customSql, array $params = []) {
        $query = "DELETE FROM " . $this->getTable() . " WHERE " . $customSql;
        return $this->query($query, $params);
    }

    /**
     * Delete a single record matching an identifier.
     *
     * @param mixed $id Identifier value to delete
     * @param string $id_column Column name representing the identifier
     * @return bool Returns true on success or false on failure
     */
    public function delete($id, $id_column = 'id') {
        $data[$id_column] = $id;
        $query = "DELETE FROM " . $this->getTable() . " WHERE $id_column = :$id_column LIMIT 1";
        return $this->query($query, $data);
    }

    /**
     * Retrieve all table records ordered descending by ID.
     *
     * @return array|bool Result set rows or false on failure
     */
    public function findAll() {
        $query = "SELECT * FROM " . $this->getTable() . " ORDER BY id DESC";
        return $this->query($query);
    }

    /**
     * Retrieve all table records without explicit ordering.
     *
     * @return array|bool Result set rows or false on failure
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->getTable();
        return $this->query($query);
    }


    /**
     * Count records using custom SQL conditions and explicit parameters.
     * Useful for ranges, inequalities (>=, <=, !=), and complex filters.
     * 
     * @param string $customSql SQL condition string (e.g., "last_activity >= :time")
     * @param array $params Associative array of bind parameters
     * @return int Total record count
     */
    public function countWhere(string $customSql, array $params = []): int {
        $query = "SELECT COUNT(*) as total FROM " . $this->getTable() . " WHERE " . $customSql;
        $result = $this->query($query, $params);
        return (int)($result[0]->total ?? 0);
    }

    /**
     * Count total rows matching optional filter criteria.
     *
     * @param array $data Associative array of filtering conditions
     * @return int Total record count
     */
    public function count($data = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->getTable();
        
        if (!empty($data)) {
            $query .= " WHERE ";
            foreach (array_keys($data) as $key) {
                $query .= $key . " = :" . $key . " AND ";
            }
            $query = trim($query, " AND ");
        }

        $result = $this->query($query, $data);
        return $result[0]->total ?? 0;
    }
    
    /**
     * Count total records supporting array conditions and raw custom SQL expressions.
     *
     * @param array|string $conditions Array of conditions or custom SQL string
     * @param string $customSql Additional custom SQL fragment
     * @return int Total record count
     */
    public function counts($conditions = [], $customSql = "") {
        if (is_string($conditions)) {
            $customSql = $conditions;
            $conditions = [];
        }

        $query = "SELECT COUNT(*) as total FROM " . $this->getTable();
        $whereClauses = [];

        if (!empty($conditions) && is_array($conditions)) {
            foreach (array_keys($conditions) as $key) {
                $whereClauses[] = "{$key} = :{$key}";
            }
        }

        if (!empty($customSql)) {
            $whereClauses[] = $customSql;
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $result = $this->query($query, $conditions);
        
        return (int)($result[0]->total ?? 0);
    }

    /**
     * Retrieve the first raw row from the table.
     *
     * @return object|false First record object or false if table is empty
     */
    public function getfirstrow() {
        $sql = "SELECT * FROM " . $this->getTable() . " LIMIT 1";
        $result = $this->query($sql);
        return $result[0] ?? false;
    }

    /**
     * Dynamically fetch records matching a set of role or status values.
     *
     * @param array $values List of values to match against the column
     * @param string $column Column name to check, defaulting to 'role'
     * @return array|bool Result set rows or false on failure
     */
    public function fetchByRoles(array $values, $column = 'role') {
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $query = "SELECT * FROM " . $this->getTable() . " 
                  WHERE {$column} IN ($placeholders) 
                  ORDER BY id DESC";

        return $this->query($query, $values);
    }

    /**
     * Calculate the sum of a specific column based on where conditions and custom constraints.
     *
     * @param array $where Associative array of match conditions
     * @param string $sumColumn Target column to compute the sum for
     * @param string $customConditions Additional raw SQL constraints
     * @return float Computed sum total
     */
    public function whereAndSum(array $where, string $sumColumn, string $customConditions = ""): float {
        $query = "SELECT SUM(`{$sumColumn}`) AS total_sum FROM " . $this->getTable() . " WHERE ";
        
        $conditions = [];
        foreach ($where as $column => $value) {
            $conditions[] = "`{$column}` = :{$column}";
        }

        $query .= implode(" AND ", $conditions);

        if (!empty($customConditions)) {
            $query .= " AND (" . $customConditions . ")";
        }

        $result = $this->query($query, $where);

        if (!empty($result) && is_array($result)) {
            $row = $result[0];
            $total = is_object($row) ? ($row->total_sum ?? 0) : ($row['total_sum'] ?? 0);
            return (float)$total;
        }

        return 0.00;
    }
}