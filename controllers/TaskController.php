<?php

class TaskController extends Controller
{
    private $model;


    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new Task();
    }

    public function create()
    {
        // Sanitize form inputs
        $this->set("POST", [
            "content" => trim($this->get("POST.content")),
            "list_id" => $this->get("POST.list_id"),
            "due_date" => $this->get("POST.due_date"),
            "priority" => $this->get("POST.priority"),
        ]);

        if ($this->isFormValid()) {
            $listId = $this->get("POST.list_id");
            // Check if list already exists

            // Save the task
            $this->model->create();
            $this->f3->reroute("@appList(@id={$listId},@mode=add)");
        }
        $this->f3->reroute("@app");
    }

    public function toggleTask()
    {
        // TODO: Check if the id corresponds to the list that belongs to the user.
        $this->model->toggleTask($this->get("PARAMS.id"));
        $this->f3->reroute("@app");
    }
    
    private function isFormValid()
    {
        $errors = [];

        if ($this->get("POST.content") == "") {
            array_push($errors, "Task content is required.");
        }
        if ($this->get("POST.list_id") == "") {
            array_push($errors, "List Id is required.");
        }

        return $this->validateForm($errors);
    }
}