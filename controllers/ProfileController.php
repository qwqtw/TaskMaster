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
        $this->set("deleteSuccessMessage", $this->get("SESSION.deleteSuccessMessage") ?? NULL);

        echo $this->template->render("index.html");
    }

    /**
     * POST: Update the user if the form validates.
     */

public function update()
{
    
    // Sanitize form inputs
    $this->set("POST", [
        "username" => trim($this->get("POST.username")),
        "password" => trim($this->get("POST.password")),
        "password-confirm" => trim($this->get("POST.password-confirm")),
    ]);

    if ($this->isFormValid()) {
        $username = $this->get("POST.username");
        $password = $this->get("POST.password");

        // Attempt to update the user
        $updateSuccess = $this->model->updateUser($username, $password);

        if ($updateSuccess) {
            // Update was successful
            $this->set("success", "User updated successfully.");
        } else {
            // Update failed
            $this->set("errors", ["Failed to update user."]);
        }
    } else {
        // Form is invalid, errors are set within isFormValid()
        $this->set("username", $this->get("POST.username"));
    }

    // Render the response for both success and failure cases
    $this->render();
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
        // Password validation
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");

        if ($pass == ""){
            array_push($errors, "Password is required.");
        }
        else if ($passConfirm == "") {
            array_push($errors, "Please confirm the password.");
        }
        // Compare password/confirm to make sure they match.
        else if (strcmp($passConfirm, $pass) != 0) {
            array_push($errors, "Password doesn't match.");
        }

        return $this->validateForm($errors);
    }
}