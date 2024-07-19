<?php

class ProfileController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new User();
    }

    /**
     * GET: Display the profile update page
     */
    public function render()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $userId = $this->get("COOKIE.user_id");
        $user = $this->model->getById($userId);

        // Setup the css
        $this->set("css", ["css/login.css"]);
        $this->setPageTitle("Update Profile");
        $this->set("form", "includes/profile-update.html");
        $this->set("container", "profile-container");
        $this->set("user", $user); // Pass user data to the view

        // Ensure successMessage, deleteSuccessMessage, and errors are set
        $this->set("successMessage", $this->get("SESSION.successMessage") ?? NULL);
        $this->set("deleteSuccessMessage", $this->get("SESSION.deleteSuccessMessage") ?? NULL);
        $this->set("errors", $this->get("SESSION.errors") ?? NULL);

        // Clear session messages
        $this->clearSessionMessages();

        echo $this->template->render("index.html");
    }

    /**
     * POST: Update user profile
     */
    public function update()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        // Sanitize form inputs
        $this->set("POST", [
            "username" => trim($this->get("POST.username")),
            "password" => trim($this->get("POST.password")),
            "password-confirm" => trim($this->get("POST.password-confirm")),
            "referer" => $this->get("POST.referer")
        ]);

        $errors = [];

        if (!$this->isFormValid()) {
            $errors = $this->get("errors");
        } else {
            // Check if the new username already exists
 $username = $this->get("POST.username");
$userId = $this->get("COOKIE.user_id");
$existingUser = $this->model->getUserByUsername($username);

if ($existingUser && $existingUser->id != $userId) {
    $errors[] = "This username is already taken.";
}

        }



        if (empty($errors)) {
            $userId = $this->get("COOKIE.user_id");
            $data = [
                "username" => $this->get("POST.username"),
                "password" => password_hash($this->get("POST.password"), PASSWORD_DEFAULT),
              
            ];
            $this->model->updateUser($userId, $data);
            $this->set("SESSION.successMessage", "Profile updated successfully.");
        } else {
            $this->set("SESSION.errors", $errors);
        }

        $this->f3->reroute("@profile");
    }

    /**
     * Clear session messages
     */
    private function clearSessionMessages()
    {
        $this->set("SESSION.successMessage", NULL);
        $this->set("SESSION.deleteSuccessMessage", NULL);
        $this->set("SESSION.errors", NULL);
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

    /**
     * Validate the data for the form after a POST method
     * @return boolean true if the form is valid
     */
    private function isFormValid()
    {
        $errors = [];

        if ($this->get("POST.username") == ""){
            array_push($errors, "Username is required.");
        }
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");
        if ($pass == ""){
            array_push($errors, "Password is required.");
        } elseif ($passConfirm == ""){
            array_push($errors, "Please confirm the password.");
        } elseif ($pass !== $passConfirm){
            array_push($errors, "Passwords do not match.");
        }

        if (!empty($errors)){
            $this->set("errors", $errors);
            return false;
        }

        return true;
    }
}