<?php

/**
 * The parent class for all models
 */
class Model extends DB\SQL\Mapper
{
    // Database connection
    protected $db;

    /**
     * Parent class constructor
     * Connect to the database
     * @params string $table name of the database to interact with
     */
 public function __construct($table)
    {
        $f3 = Base::instance();

        // Parse DATABASE_URL from environment
        $db_url = getenv('DATABASE_URL'); // Make sure Heroku sets this
        $parsed_url = parse_url($db_url);

        // Ensure the array keys exist before accessing them
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : null;
        $dbname = isset($parsed_url['path']) ? ltrim($parsed_url['path'], '/') : null;
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : null;
        $password = isset($parsed_url['pass']) ? $parsed_url['pass'] : null;
        $port = isset($parsed_url['port']) ? $parsed_url['port'] : null;

        // Check if any critical value is missing
        if (!$host || !$dbname || !$user || !$password || !$port) {
            throw new Exception('Database connection details are missing.');
        }

        // Connect to PostgreSQL database
        $this->db = new DB\SQL(
            "pgsql:host={$host};dbname={$dbname};port={$port}",
            $user,
            $password
        );

        // Create mapper of the given table
        parent::__construct($this->db, $table);
    }

    /**
     * Get user_id = {id} prefilled with logged in user for WHERE clauses.
     * @return string the filled user_id = {id}
     */
    public function getUserQuery()
    {
        return "user_id = " . $_SESSION["userId"];
    }

    /**
     * Get all entries from the table for the user.
     */
    public function getAll()
    {
        $this->load();
        return $this->query;
    }

    /**
     * Fetch a single value from the table, using id primary key.
     * @param int id row to fetch
     * @return Object database result
     */
    public function getById($id)
    {
        return $this->findone(["id = ?", $id]);
    }

    /**
     * Delete a row from the table using id primary key.
     * @param int id row to delete
     * @return int success feedback
     */
    public function deleteById($id)
    {
        $this->load(["id = ?", $id]); // Load the object
        return $this->erase();
    }
}