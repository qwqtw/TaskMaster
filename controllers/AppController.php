<?php

class AppController extends Controller
{
    private $lists;
    private $task;


    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->lists = new Lists();
        $this->task = new Task();
    }

    /**
     * Render the app page.
     */
    public function render()
    {
        // Get the lists
        $this->set("lists", $this->lists->getAll());
        $this->set("mode", (isset($_SESSION["mode"])) ? $_SESSION["mode"] : "all");
        $this->set("byPriority", (isset($_SESSION["byPriority"])) ? $_SESSION["byPriority"] : false);
        $this->set("tasks", []);

        // Load from the session
        if (isset($_SESSION["listId"])) {
            $list = $this->lists->getById($_SESSION["listId"]);
            $this->loadList($list);
        }
        // Load the first list
        else {
            $list = $this->lists->getFirstList();
            $this->loadList($list);
        }

        // Setup the css needed
        $this->set("css", ["css/app.css"]);
        $this->set("container", "app-container");
        // TODO: Set the username
        $this->set("username", "test");
        echo $this->template->render("app.html");
    }

    /**
     * Remember the selected mode (all, active, completed)
     */
    public function setMode()
    {
        if (array_key_exists("mode", $this->get("PARAMS"))) {
            // TODO: Ensure mode is a proper value.
            $_SESSION["mode"] = $this->get("PARAMS.mode");
        }
        $this->f3->reroute("@app");
    }

    /**
     * Remember the selected list in the session.
     */
    public function setList()
    {
        if (array_key_exists("id", $this->get("PARAMS"))) {
            
            $list = $this->lists->getById($this->get("PARAMS.id"));
            // Validate the list id exists
            if ($list) {
                $_SESSION["listId"] = $this->get("PARAMS.id");     
            }
        }
        $this->f3->reroute("@app");
    }

    /**
     * Remember the order by priority state.
     */
    public function setByPriority()
    {
        $_SESSION["byPriority"] = isset($_SESSION["byPriority"]) ? !$_SESSION["byPriority"] : true;
    }

    /**
     * Load the selected list and it's tasks
     * @param Object $list the list object from the database
     */
    private function loadList($list)
    {
        $this->set("selectedTitle", $list["title"]);
        $this->set("selectedId", $list["id"]);
        
        $mode = $this->get("mode");
        $byPriority = $this->get("byPriority");

        if ($mode == "all") {
            $this->set("tasks", $this->task->getTasksAll($list["id"], $byPriority));
        }
        else if ($mode == "active") {
            $this->set("tasks", $this->task->getTasksActive($list["id"], $byPriority));
        }
        else if ($mode == "completed") {
            $this->set("tasks", $this->task->getTasksCompleted($list["id"], $byPriority));
        }
    }
}