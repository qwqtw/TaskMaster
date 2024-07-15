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

    public function app($f3)
    {
        echo $this->template->render("main.html");
    }
}
