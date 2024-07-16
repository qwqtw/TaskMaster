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

     /**
     * Update the user's profile picture.
     * @param int $userId User's ID.
     * @param string $filePath New file path for the profile picture.
     */
    public function updateProfilePicture($userId, $filePath)
    {
        $user = $this->load(['id=?', $userId]);
        $user->profile_picture = $filePath;
        $user->save();
    }

    /**
     * Update the user's credentials (username and password).
     * @param int $userId User's ID.
     * @param string $username New username.
     * @param string $hashedPassword New hashed password.
     */
    public function updateCredentials($userId, $username, $hashedPassword)
    {
        $user = $this->load(['id=?', $userId]);
        $user->username = $username;
        $user->password = $hashedPassword;
        $user->save();
    }

    /**
     * Delete a user from the database.
     * @param int $userId User's ID.
     */
    public function deleteUser($userId)
    {
        $user = $this->load(['id=?', $userId]);
        if ($user) {
            $user->erase();
        }
    }

    /**
     * Check if the username is available for registration.
     * @param string $username Username to check.
     * @return bool Returns true if the username is available.
     */
    public function isUsernameAvailable($username)
    {
        $user = $this->findone(['username=?', $username]);
        return $user === null; // If no user is found, the username is available
    }
}