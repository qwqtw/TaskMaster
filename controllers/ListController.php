<?php

class ListController extends Controller
{
    private $model;
    private $task;


    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new Lists();
        $this->task = new Task();
    }

    public function create()
    {
        // Sanitize form inputs
        $this->set("POST", [
            "title" => trim($this->get("POST.title")),
            "user_id" => $this->get("COOKIE.user_id"),
        ]);

        if ($this->isFormValid()) {
            // Check if list already exists

            // Save the list
            $listId = $this->model->create();
            $this->f3->reroute("@appList(@id={$listId})");
        }
        $this->f3->reroute("@app");
    }

    private function isFormValid()
    {
        $errors = [];

        if ($this->get("POST.title") == "") {
            array_push($errors, "List name is required.");
        }

        return $this->validateForm($errors);
    }
}