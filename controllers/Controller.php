<?php
// Parent controller class

class Controller
{
    protected $f3;
    protected $template;


    /**
     * Parent class constructor. Set up template.
     * Default page title.
     * @param Object $f3 the FatFree Framework instance
     */
    public function __construct($f3) 
    {
        $this->f3 = $f3;
        
        $f3->set("pageTitle", $this->get("SITENAME"));
        $f3->set("errors", []);

        // Setup template
        $this->template = new Template;
    }

    /**
     * Get value from the $f3 array.
     * @param string $key part of the $f3 instance
     * @return mixed the value that corresponds to the key
     */
    public function get($key)
    {
        return $this->f3->get($key);
    }
    /**
     * Set value in the $f3 array.
     * @param string $key the key to save to the $f3 instance
     * @param mixed $value value to set for the given $key
     */
    public function set($key, $value)
    {
        $this->f3->set($key, $value);
    }

    /**
     * Verify if there is a user that is logged in
     * through cookies.
     * @return bool true if user is logged in.
     */
    public function isLoggedIn()
    {
        return $this->get("COOKIE.auth");
    }

    /**
     * Take the default and format it with the new title = "title | default title"
     */
    public function setPageTitle($title)
    {
        $currentTitle = $this->get("pageTitle");
        $newTitle = $title;

        if ($currentTitle != "") {
            $newTitle .= " | " . $currentTitle;
        }

        $this->set("pageTitle", $newTitle);
    }

    /**
     * Checks if there's errors in $formErrors and set up the f3 errors property if there's any.
     * @param array $formErrors pass your errors array.
     * @return bool true if $forms has no errors and false if it does, as well as set the f3 errors property.
     */
    public function validateForm($formErrors)
    {
        if (!empty($formErrors)) {
            $this->set("errors", $formErrors);
            return false;
        }
        return true;
    }
}
