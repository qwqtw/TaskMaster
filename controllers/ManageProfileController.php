<?php

class ManageProfileController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new User();
    }

    /**
     * GET: Display the profile management page
     */
    public function render()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $this->setPageTitle("Manage Profile");
        $this->set("form", "includes/header.html");
        $this->set("container", "manage-profile-container");

        echo $this->template->render("contact-us.html");
    }

    /**
     * POST: Update the profile if the form validates.
     */
    public function updateProfile()
    {
        // Sanitize form inputs
        $this->set("POST", [
            "username" => trim($this->get("POST.username")),
            "password" => trim($this->get("POST.password")),
            "password-confirm" => trim($this->get("POST.password-confirm")),
        ]);

        if ($this->isFormValid()) {
            // Update user in database
            $updateResult = $this->model->updateProfile(
                $_SESSION['user_id'],
                $this->get("POST.username"),
                $this->get("POST.password")
            );

            if ($updateResult) {
                $this->f3->reroute("@app");
            } else {
                $this->set("errors", ["Failed to update profile."]);
            }
        }
        // Form is invalid
        else {
            $this->set("username", $this->get("POST.username"));
        }
        // Render the profile management page again
        $this->render();
    }

    /**
     * POST: Delete the account.
     */
    public function deleteAccount()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Delete the user account from the database
            $deleteResult = $this->model->deleteAccount($_SESSION['user_id']);

            if ($deleteResult) {
                session_destroy(); // Destroy session after account deletion
                $this->f3->reroute("@home");
            } else {
                $this->set("errors", ["Failed to delete account."]);
                // Render the profile management page again
                $this->render();
            }
        }
    }

    /**
     * Validate the data for the form after a POST method
     * @return boolean true if the form is valid
     */
    private function isFormValid()
    {
        $errors = [];

        if ($this->get("POST.username") == "") {
            array_push($errors, "Username is required.");
        }
        // Password validation
        $pass = $this->get("POST.password");
        $passConfirm = $this->get("POST.password-confirm");

        if ($pass == "") {
            array_push($errors, "Password is required.");
        } else if ($passConfirm == "") {
            array_push($errors, "Please confirm the password.");
        } // Compare password/confirm to make sure they match.
        else if (strcmp($passConfirm, $pass) != 0) {
            array_push($errors, "Passwords do not match.");
        }

        if (!empty($errors)) {
            $this->set("errors", $errors);
            return false;
        }

        return true;
    }
}
