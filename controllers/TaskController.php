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
        if (isset($_SESSION["listId"])) {
            // Sanitize form inputs
            $this->set("POST", [
                "list_id" => $_SESSION["listId"],
                "content" => trim($this->get("POST.content")),
                "due_date" => $this->get("POST.due_date"),
                "priority" => $this->get("POST.priority"),
            ]);

            if ($this->isFormValid()) {
                $listId = $this->get("POST.list_id");
                // Check if list already exists

                // Save the task
                $taskId = $this->model->create();
                $this->f3->reroute("@app#t-{$taskId}");
            }
        }
        $this->f3->reroute("@app");
    }

    public function toggleTask()
    {
        // TODO: Check if the id corresponds to the list that belongs to the user.
        $is_completed = $this->model->toggleTask($this->get("PARAMS.id"));
        // Receive feedback on the front end ajax
        echo $is_completed;
    }

    public function delete()
    {
        // Add validation that the id belongs to the list that belongs to the user.
        $this->model->deleteById($this->get("PARAMS.id"));
        // Receive feedback on the front end ajax
        echo 1;
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