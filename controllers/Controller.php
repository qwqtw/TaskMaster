<?php
// Parent controller class

class Controller {

    protected $f3;
    protected $template;

    function __construct($f3) 
    {
        $this->f3 = $f3;
        
        $f3->set("pageTitle", "TaskMaster");

        $f3->set("errors", "");

        // Setup template
        $this->template = new Template;
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

    public function setPageTitle($title)
    {
        $currentTitle = $this->f3->get("pageTitle");
        $newTitle = $title;

        if ($currentTitle != "") {
            $newTitle .= " | " . $currentTitle;
        }

        $this->f3->set("pageTitle", $newTitle);
    }
}
