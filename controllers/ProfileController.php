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

        echo $this->template->render("index.html");
    }
       /**
     * Clear session messages
     */
    private function clear()
    {
        $this->set("SESSION.successMessage", NULL);
        $this->set("SESSION.deleteSuccessMessage", NULL);
        $this->set("SESSION.errors", NULL);
    }

public function update()
    {
        $this->set("POST", [
            "username" => trim($this->get("POST.username")),
            "password" => trim($this->get("POST.password")),
            "password-confirm" => trim($this->get("POST.password-confirm")),
        ]);

        if ($this->isFormValid()) {
            $username = $this->get("POST.username");
            $password = $this->get("POST.password");
            $userId = $this->get("COOKIE.user_id");

            $updateSuccess = $this->model->updateUser($userId, $username, $password);

            if ($updateSuccess) {
                $this->set("SESSION.successMessage", "User updated successfully.");
                $this->f3->reroute("@profile");
            } else {
                $this->set("SESSION.errors", ["Failed to update user. The username might already be taken."]);
            }
        } else {
            $this->set("username", $this->get("POST.username"));
        }

        $this->render();
    }

    /**
     * POST: Delete user account
     */
    public function delete()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $userId = $this->get("COOKIE.user_id");
        $this->model->deleteUser($userId);

        // Expire cookies
        $expiration = time() - 1;
        setcookie("auth", "", $expiration);
        setcookie("user_id", "", $expiration);
        
        // Set delete success message
        $this->set("SESSION.deleteSuccessMessage", "Account deleted successfully.");
        
        $this->f3->reroute("@home");
    }


    private function isFormValid()
    {
        $errors = [];
    
        // Password validation
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");

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
