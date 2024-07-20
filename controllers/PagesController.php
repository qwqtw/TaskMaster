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

    /**
     * Log out the user
     */
    public function logout()
    {
        // Destroy all session variables.
        session_unset();
        // Destroy the session file from the server.
        session_destroy();

        $this->f3->reroute("@home");
    }
}
