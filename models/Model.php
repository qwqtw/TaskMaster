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

        // Parse DATABASE_URL
        $db_url = getenv('postgres://uf25fqbbno2qgh:pa633282d2ad1ab62c5f9c0f9c433e3d01c724a8fd70d6426cc456467f057ad4f@c5flugvup2318r.cluster-czrs8kj4isg7.us-east-1.rds.amazonaws.com:5432/d5ib0k4kofiqoc');
        $parsed_url = parse_url($db_url);

        $host = $parsed_url['host'];
        $dbname = ltrim($parsed_url['path'], '/');
        $user = $parsed_url['user'];
        $password = $parsed_url['pass'];
        $port = $parsed_url['port'];

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