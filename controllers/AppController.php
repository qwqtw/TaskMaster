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
        $currentLists = $this->lists->getAll();

        // If the user has no list, create the default one.
        if (empty($currentLists)) {

            $_SESSION["listId"] = null;
            $this->lists->createDefault();
            $currentLists = $this->lists->getAll();
        }

        $this->set("lists", $currentLists);
        $this->set("mode", (isset($_SESSION["mode"])) ? $_SESSION["mode"] : "all");
        $this->set("byPriority", (isset($_SESSION["byPriority"])) ? $_SESSION["byPriority"] : false);
        $this->set("byDueDate", (isset($_SESSION["byDueDate"])) ? $_SESSION["byDueDate"] : false);
        $this->set("tasks", []);

        // Load from the session
        if (isset($_SESSION["listId"])) {

            $list = $this->lists->getById($_SESSION["listId"]);
            $this->loadList($list);
        }
        // Load the first list
        else {
            $list = $this->lists->getFirstList();
            $_SESSION["listId"] = $list["id"];
            $this->loadList($list);
        }

        // Setup the css needed
        $this->set("css", [
            "https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css", 
            "css/app.css", 
            "css/app-tasks.css"
        ]);
        $this->set("container", "app-container");
        $this->set("username", isset($_SESSION["username"]) ? $_SESSION["username"] : "user");
        $this->set("avatar", isset($_SESSION["avatar"]) ? $_SESSION["avatar"] : "public/images/avatar.png");

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
                $this->f3->reroute("@app#l-" . $this->get("PARAMS.id"));
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
        $this->f3->reroute("@app");
    }

    public function setByDueDate()
    {
        $_SESSION["byDueDate"] = isset($_SESSION["byDueDate"]) ? !$_SESSION["byDueDate"] : true;
        $this->f3->reroute("@app");
    }

    /**
     * Load the selected list and it's tasks
     * @param Object $list the list object from the database
     */
    private function loadList($list)
    {
        $listId = $list["id"];
        $this->set("selectedTitle", $list["title"]);
        $this->set("selectedId", $listId);
        
        $mode = $this->get("mode");
        $this->task->setOptions($this->get("byPriority"), $this->get("byDueDate"));

        switch($mode) {
            case "all":
                $taskList = $this->task->getTasksAll($listId);
                break;
            case "active":
                $taskList = $this->task->getTasksActive($listId);
                break;
            case "completed":
                $taskList = $this->task->getTasksCompleted($listId);
                break;
            default:
                return;
        }
        $this->set("selectedCount", $this->task->countTasksActive($listId));
        $this->set("tasks", $taskList);
    }
}