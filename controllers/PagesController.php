<?php

// Handle non-database pages
class PagesController extends Controller 
{
    public function contactUs($f3) 
    {
        // Setup the css
        $this->set("css", ["css/contact-us.css"]);
        $this->setPageTitle("Contact Us");
        // Determine which header to use based on login status
        if ($this->isLoggedIn()) {
            $headerFile = "includes/header.html"; // Path for logged in users
        } else {
            $headerFile = "includes/header-guest.html"; // Path for guests
        }
        $this->set("header", $headerFile);
        $this->set("username", "test");
        echo $this->template->render("contact-us.html");
    }



    public function app()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $this->set("container", "app-container");
        $this->set("username", "test");
        echo $this->template->render("app.html");
    }

    /**
     * Log out the user
     */
    public function logout()
    {
        // Expire cookies
        $expiration = time() - 1;
        setcookie("auth", "", $expiration);
        setcookie("user_id", "", $expiration);
        $this->f3->reroute("@home");
    }
}
