<?php

// Handle non-database pages
class PagesController extends Controller 
{
    public function contactUs($f3) 
    {
        $this->setPageTitle("Contact us");
        echo $this->template->render("contact-us.html");
    }

    public function contactUsGuest($f3) 
    {
        $this->setPageTitle("Contact us");
        echo $this->template->render("contact-us-guest.html");
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
