<?php

// Handle non-database pages
class PagesController extends Controller 
{
    public function contactUs($f3) 
    {
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
        // Expire cookies
        $expiration = time() - 1;
        setcookie("auth", "", $expiration);
        setcookie("user_id", "", $expiration);
        $this->f3->reroute("@home");
    }
}
