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

        $this->db = new DB\SQL("mysql:host=localhost;dbname={$f3->get('DBNAME')};port={$f3->get('DBPORT')}", 
            $f3->get("DBUSER"), 
            $f3->get("DBPASS"));

        // Create mapper of the given table
        parent::__construct($this->db, $table);
    }


    public function getAll()
    {
        $this->load();
        return $this->query;
    }

    /**
     * Fetch a single value from the table, using id primary key
     * @param int id row to fetch
     * @return Object database result
     */
    public function getById($id)
    {
        return $this->findone(["id = ?", $id]);
    }


    /**
     * Insert a new row into the table using POST data
     * @return int last inserted id
     */
    public function addEntry()
    {
        $this->copyfrom("POST");
        $this->save();

        return $this->id; // last inserted id
    }
}