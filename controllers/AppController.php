<?php

class AppController extends Controller
{
    private $model;

    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new Lists();
    }

    public function render()
    {
        if (!$this->isLoggedIn()) {
            $this->f3->reroute("@home");
        }

        $lists = $this->model->getAll();
        $this->set("lists", $lists);

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

        // Check if list already exists

        // Save the list
        $this->model->createList();
        $this->f3->reroute("@app");
    }
}