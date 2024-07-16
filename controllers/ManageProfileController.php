<?php

class ManageProfileController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new User();
    }

    public function changeProfilePicture()
    {
        $userId = $this->f3->get('SESSION.user_id');
        $uploadedFile = $_FILES['profile_picture'];
    
        if ($uploadedFile['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $uploadedFile['tmp_name'];
            $name = basename($uploadedFile['name']);
    
            // Validate file type, size, etc., here
    
            $uploadDir = 'path/to/uploads/';
            if (move_uploaded_file($tmp_name, $uploadDir . $name)) {
                $this->model->updateProfilePicture($userId, $uploadDir . $name);
                $this->f3->reroute('@profile');
            } else {
                $this->f3->set('error', 'File upload failed.');
            }
        } else {
            $this->f3->set('error', 'Error uploading file.');
        }
    }
    
    public function changeCredentials()
    {
        $userId = $this->f3->get('SESSION.user_id');
        $newUsername = trim($this->f3->get('POST.username'));
        $newPassword = trim($this->f3->get('POST.password'));
    
        // Check if the username is not taken, and validate password as needed
        if ($this->model->isUsernameAvailable($newUsername) && $this->isPasswordValid($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->model->updateCredentials($userId, $newUsername, $hashedPassword);
            $this->f3->reroute('@profile');
        } else {
            $this->f3->set('error', 'Username taken or invalid password.');
        }
    }
    
    public function deleteAccount()
    {
        $userId = $this->f3->get('SESSION.user_id');
        
        if ($this->model->deleteUser($userId)) {
            $this->f3->clear('SESSION');  // Clear user session
            $this->f3->reroute('@home');
        } else {
            $this->f3->set('error', 'Failed to delete account.');
        }
    }
    
}
