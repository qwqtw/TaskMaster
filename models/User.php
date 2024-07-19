<?php

class User extends Model
{
    public function __construct()
    {
        parent::__construct("user");
    }

    /**
     * Create the user entry and hash the password.
     * @return int id of the newly created user
     */
    public function createUser()
    {
        $this->copyfrom("POST");
        // Hash the password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        // Current date
        $this->reg_date = date("Y-m-d");
        
        $this->save();
        return $this->id;
    }

    /**
     * Get user by it's username.
     * @param string $username the user's username
     * @return Object result
     */
    public function getUserByUsername($username)
    {
        return $this->findone(["username = ?", $username]);
    }


    // -------------------------------

        /**
     * Update user details.
     * @param int $id user ID
     * @param array $data array of fields to update
     * @return void
     */
    public function updateUser($id, $data)
    {
        $this->load(["id = ?", $id]);
        $this->copyfrom($data);
        $this->save();
    }

    /**
     * Delete user by ID.
     * @param int $id user ID
     * @return void
     */
    public function deleteUser($id)
    {
        $this->load(["id = ?", $id]);
        $this->erase();
    }

}