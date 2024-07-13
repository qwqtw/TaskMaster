<?php

// Handle non-database pages
class PagesController extends Controller 
{
    public function login($f3)
    {
        $this->setPageTitle("Login");
        $f3->set("form", "includes/login.html");
        $f3->set("container", "login-container");

        echo $this->template->render("index.html");
    }

    public function register($f3)
    {
        $this->setPageTitle("Register");
        $f3->set("form", "includes/register.html");
        $f3->set("container", "register-container");

        echo $this->template->render("index.html");
    }

    public function contactUs($f3) 
    {
        $this->setPageTitle("Contact us");
        echo $this->template->render("contact-us.html");
    }
}
