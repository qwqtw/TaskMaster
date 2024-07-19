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

    public function render()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $this->set("lists", $this->lists->getAll());
        $this->set("tasks", []);

        if (array_key_exists("mode", $this->get("PARAMS"))) {
            $this->set("modeAdd", true);
        }
        // GET: List by id
        if (array_key_exists("id", $this->get("PARAMS"))) {      
            /*echo "hello world";
            echo $this->get("PARAMS.id");
            die();*/
            $list = $this->lists->getById($this->get("PARAMS.id"));
            if ($list) {
                $this->loadList($list);
            }
        }
        // Load the first list
        else {
            $list = $this->lists->getFirstList();
            $this->loadList($list);
        }

        $this->set("container", "app-container");
        $this->set("username", "test");
        echo $this->template->render("app.html");
    }

    public function loadList($list)
    {
        $this->set("selectedTitle", $list["title"]);
        $this->set("selectedId", $list["id"]);
        
        $this->set("tasks", $this->task->getTasks($list["id"]));
    }
}