<?php
class ProfileController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new User();
    }

    public function render()
    {
        
        $userId = $_SESSION["userId"];
        $user = $this->model->getById($userId);

        // Setup the css
        $this->set("css", ["css/login.css"]);
        $this->setPageTitle("Update Profile");
        $this->set("form", "includes/profile-update.html");
        $this->set("container", "profile-container");
        $this->set("user", $user); // Pass user data to the view

        // Ensure successMessage, deleteSuccessMessage, and errors are set
        $this->set("successMessage", $this->get("SESSION.successMessage") ?? NULL);
        $this->clear("SESSION.successMessage"); // Clear the success message from the session
        $this->set("deleteSuccessMessage", $this->get("SESSION.deleteSuccessMessage") ?? NULL);
        $this->clear("SESSION.deleteSuccessMessage"); // Clear the delete success message from the session

        echo $this->template->render("index-profile.html");
    }
       /**
     * Clear session messages
     */
  private function clear($key)
    {
        $this->set($key, NULL);
    }


    public function update()
        {
            $this->set("POST", [
                "username" => trim($this->get("POST.username")),
                "password" => trim($this->get("POST.password")),
                "password-confirm" => trim($this->get("POST.password-confirm")),
            ]);

            $avatar = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = $this->uploadAvatar($_FILES['avatar']);
        }

            if ($this->isFormValid()) {
                $username = $this->get("POST.username");
                $password = $this->get("POST.password");
                $userId = $_SESSION["userId"];

                $updateSuccess = $this->model->updateUser($userId, $username, $password, $avatar);

                if ($updateSuccess) {
                    if ($avatar) {
                        $_SESSION['avatar'] = $avatar;
                    }
                    $this->set("SESSION.successMessage", "User updated successfully.");
                    $this->f3->reroute("@profile");
                } 
            } else {
                $this->set("username", $this->get("POST.username"));
            }

            $this->render();
    }

    private function uploadAvatar($file)
    {
        $targetDir = "public/images/avatars/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return null;
        }

        // Check file size (limit to 2MB)
        if ($file["size"] > 2000000) {
            return null;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            return null;
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            $targetFile = $targetDir . uniqid() . "." . $imageFileType;
        }

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            return null;
        }
    }

    /**
     * GET: Delete user account
     */
    public function delete()
    {
        $userId = $_SESSION["userId"];
        $this->model->deleteUser($userId);
        $this->f3->reroute("@logout");
    }


    private function isFormValid()
    {
        $errors = [];
        
        // Get the username from POST
        $username = $this->get("POST.username");

        // Password validation
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");

        // Check if username already exists in the database
        if ($username) {
            $existingUser = $this->model->getUserByUsername($username);
        
            if (!empty($existingUser)) {
                array_push($errors, "Username already exists.");
            }
        } 

        if ($pass && $passConfirm == "") {
            array_push($errors, "Please confirm the password.");
        } 
        // Compare password/confirm to make sure they match.
        else if (strcmp($passConfirm, $pass) != 0) {
            array_push($errors, "Password doesn't match.");
        }

        return $this->validateForm($errors);
    }

}
