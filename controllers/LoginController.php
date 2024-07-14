<?php

class LoginController extends Controller
{
    // Sanitized
    private $post = [];

    /**
     * GET: Display the login page
     */
    public function render()
    {
        $this->setPageTitle("Login");
        $this->set("form", "includes/login.html");
        $this->set("container", "login-container");

        echo $this->template->render("index.html");
    }

    /**
     * POST: Authenticate the user
     */
    public function login()
    {
        $this->post = [
            "username" => trim($this->get("POST.username")),
            "password" => trim($this->get("POST.password")),
        ];

        if ($this->isFormValid()) {
            // TODO: Authenticate with database
            //$this->post;
    
            // redirect user
            $this->f3->reroute("@main");
        }
        // Form is not valid, reload and reset.
        else {
            $this->set("username", $this->post["username"]);
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

        // Validate username
        if ($this->post["username"] == ""){
            array_push($errors, "Username is required.");
        }
        // Validate password
        if ($this->post["password"] == ""){
            array_push($errors, "Password is required.");
        }


        if (!empty($errors)) {
            $this->set("errors", $errors);
            return false;
        }

        return true;
    }
}