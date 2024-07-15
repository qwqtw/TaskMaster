<?php
// Parent controller class

class Controller
{
    protected $f3;
    protected $template;


    public function __construct($f3) 
    {
        $this->f3 = $f3;
        
        $f3->set("pageTitle", "TaskMaster");
        $f3->set("errors", []);

        // Setup template
        $this->template = new Template;
    }

    /**
     * Get value from the $f3 array
     */
    public function get($key)
    {
        return $this->f3->get($key);
    }
    /**
     * Set value in the $f3 array
     */
    public function set($key, $value)
    {
        $this->f3->set($key, $value);
    }

    /**
     * Run before route happens
     */
    /*
    public function beforeRoute()
    {
        echo "hello world!!";
    }
    */

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
}
