<?php

class RegisterController extends Controller
{
    private $post = [];

    /**
     * GET: Display the register page
     */
    public function render()
    {
        $this->setPageTitle("Register");
        $this->set("form", "includes/register.html");
        $this->set("container", "register-container");

        echo $this->template->render("index.html");
    }

    /**
     * POST: Register the user if the form validates.
     */
    public function register()
    {
        $this->post = [
            "username" => trim($this->get("POST.username")),
            "email" => trim($this->get("POST.email")),
            "password" => trim($this->get("POST.password")),
            "password-confirm" => trim($this->get("POST.password-confirm")),
        ];

        if ($this->isFormValid()) {
            // Add user to database
            //$this->post;

            $this->f3->reroute("@home");
        }
        else {
            $this->set("username", $this->post["username"]);
            $this->set("email", $this->post["email"]);
            $this->render();
        }
    }

    /**
     * Validate the data for the form after a POST method
     * @return boolean true if the form is valid
     */
    private function isFormValid()
    {
        $errors = [];

        if ($this->post["username"] == ""){
            array_push($errors, "Username is required.");
        }
        if ($this->post["email"] == ""){
            array_push($errors, "Email is required.");
        }
        // Password validation
        if ($this->post["password"] == ""){
            array_push($errors, "Password is required.");
        }
        else if ($this->post["password-confirm"] == "") {
            array_push($errors, "Please confirm the password.");
        }
        // Compare password/confirm to make sure they match.
        else if (strcmp($this->post["password-confirm"], $this->post["password"]) != 0) {
            array_push($errors, "Password doesn't match.");
        }


        if (!empty($errors)) {
            $this->set("errors", $errors);
            return false;
        }

        return true;
    }
}