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
        $this->set("selectedTasks", []);

        // GET: List by id
        if (array_key_exists("id", $this->get("PARAMS"))) {        
            $list = $this->lists->getById($this->get("PARAMS.id"));
            if ($list) {
                $this->set("selectedTitle", $list["title"]);
                $this->set("selectedId", $list["id"]);
                
                $this->set("tasks", $this->task->getTasks($list["id"]));
            }
        }

        $this->set("container", "app-container");
        $this->set("username", "test");
        echo $this->template->render("app.html");
    }

    public function createList()
    {
        // Sanitize form inputs
        $this->set("POST", [
            "title" => trim($this->get("POST.title")),
            "user_id" => $this->get("COOKIE.user_id"),
        ]);

        if ($this->isFormValid()) {
            // Check if list already exists

            // Save the list
            $this->lists->createList();
        }
        $this->f3->reroute("@app");      
    }

    public function isFormValid()
    {
        $errors = [];

        if ($this->get("POST.title") == "") {
            array_push($errors, "List name is required.");
        }

        return $this->validateForm($errors);
    }
}