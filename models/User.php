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
     * Update the user entry with the given ID.
     * @param int $userId the user's ID
     * @param string|null $username the user's new username
     * @param string|null $password the user's new password
     * @return boolean true if the update was successful, false otherwise
     */
  public function updateUser($userId, $username = null, $password = null)
    {
        // Load the user by ID
        $user = $this->load(["id = ?", $userId]);

        if ($user) {
            if ($username) {
                // Check if the new username already exists for another user
                $existingUser = $this->findone(["username = ? AND id != ?", $username, $userId]);
                if ($existingUser) {
                    // Username is already taken by another user
                    return false; // Or return an appropriate error message
                }
                $user->username = $username;
            }
            if ($password) {
                // Hash the new password
                $user->password = password_hash($password, PASSWORD_DEFAULT);
            }
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
                // Delete all related tasks and lists before deleting the user
        $this->db->exec("DELETE FROM task WHERE list_id IN (SELECT id FROM list WHERE user_id = ?)", $id);
        $this->db->exec("DELETE FROM list WHERE user_id = ?", $id);

        $this->load(["id = ?", $id]);
        $this->erase();
    }

}