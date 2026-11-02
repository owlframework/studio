<?php
namespace Core;

// Prevent direct script access by verifying application root definition.
defined('ROOTPATH') OR die("Direct access denied.");

class Database {

    private $connection;

    public function __construct() {
        // Automatically establish a database connection on instantiation.
        $this->connect();
    }

    private function connect() {
        $str = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        try {
            $this->connection = new \PDO($str, DB_USER, DB_PASS);
            $this->connection->exec("SET time_zone = '+01:00';"); // Set session timezone to UTC +1 (Lagos)
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            if (DEBUG) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }

    /**
     * Execute a parameterized SQL statement with data bindings.
     * 
     * Handles both SELECT and modification queries securely via prepared statements.
     *
     * @param string $query SQL statement template
     * @param array $data Parameters to bind to the query
     * @return array|bool Returns result set rows for SELECT queries, boolean status for mutations, or false on failure
     */
    public function query($query, $data = []) {
        $con = $this->connection;
        $stm = $con->prepare($query);

        $check = $stm->execute($data);

        if (!$check) {
            return false;
        }

        // Determine if the executed query is a SELECT statement.
        $isSelect = (stripos(trim($query), 'SELECT') === 0);

        if ($isSelect) {
            // Return all fetched result rows as objects.
            return $stm->fetchAll(); 
        }

        // Return success indicator for write operations (INSERT, UPDATE, DELETE).
        return true; 
    }
    
    /**
     * Execute a query and return results as a key-value pair array.
     *
     * @param string $query SQL statement template
     * @param array $data Parameters to bind to the query
     * @return array Associative array of [key => value] pairs
     */
    public function queryKeyPair($query, $data = []) {
        $con = $this->connection;
        $stm = $con->prepare($query);
        $check = $stm->execute($data);

        if ($check) {
            // Fetch results formatted as a single-dimensional key-value map.
            return $stm->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        return [];
    }

    /**
     * Initiate a database transaction.
     *
     * @return bool Returns true on success or false on failure
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit the active database transaction.
     *
     * @return bool Returns true on success or false on failure
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Determine whether the connection is currently inside a transaction.
     *
     * @return bool Returns true if a transaction is active, false otherwise
     */
    public function inTransaction() {
        return $this->connection->inTransaction();
    }

    /**
     * Roll back the active database transaction.
     *
     * @return bool Returns true on success or false on failure
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * Retrieve the ID of the last inserted row or sequence value.
     *
     * @param string|null Name of the sequence object from which the ID should be returned
     * @return string|false Returns the last insert ID string or false on failure
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}