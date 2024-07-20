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
     * Update the user entry with the given username and password.
     * @param string $username the user's username
     * @param string $password the user's new password
     * @return boolean true if the update was successful, false otherwise
     */
    public function updateUser($username, $password)
    {
        // Find the user by username
        $user = $this->getUserByUsername($username);
        
        if ($user) {
            // Hash the new password
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $user->save();
            return true;
        } else {
            return false;
        }
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