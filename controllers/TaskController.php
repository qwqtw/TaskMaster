<?php

class TaskController extends Controller
{
    private $model;


    public function __construct($f3)
    {
        parent::__construct($f3);
        $this->model = new Task();
    }

    public function getTask()
    {
        if (array_key_exists("id", $this->get("PARAMS"))) {
            $task = $this->model->getById($this->get("PARAMS.id"));
            $this->echoJSON($task->cast());
        }
    }

    /**
     * Create a task.
     */
    public function createTask()
    {
        if (isset($_SESSION["listId"])) {
            // Sanitize form inputs
            $this->set("POST", [
                "list_id" => $_SESSION["listId"],
                "content" => trim($this->get("POST.content")),
                "due_date" => trim($this->get("POST.due_date")),
                "priority" => (int) $this->get("POST.priority"),
            ]);

            if ($this->isFormValid()) {
                // Check if list already exists

                // Save the task
                $taskId = $this->model->create();
                $task = $this->model->getById($taskId);
                $taskArray = $task->cast();
                // Build the baseTask url for the li.
                $taskArray["base_task_url"] = $this->get("BASE") . $this->f3->alias("getTask", "id = " . $task["id"]);

                return $this->echoJSON($taskArray);
                //$this->f3->reroute("@app#t-{$taskId}");
            }
        }
        //$this->f3->reroute("@app");
        echo "{}";
    }

    public function updateTask()
    {
        if (isset($_SESSION["listId"]) && array_key_exists("id", $this->get("POST"))) {

            $taskId = $this->get("POST.id");
            $this->set("POST", [
                "list_id" => $_SESSION["listId"],
                "content" => trim($this->get("POST.content")),
                "due_date" => trim($this->get("POST.due_date")),
                "priority" => (int) $this->get("POST.priority"),
            ]);

            if ($this->isFormValid()) {

                $task = $this->model->updateTask($taskId);
                //$task = $this->model->getById($taskId);
                $taskArray = $task->cast();
                $taskArray["base_task_url"] = $this->get("BASE") . $this->f3->alias("getTask", "id = " . $task["id"]);

                return $this->echoJSON($taskArray);
            }
        }
        echo "{}";
    }

    /**
     * Toggle the is_completed status.
     */
    public function toggleTask()
    {
        // TODO: Check if the id corresponds to the list that belongs to the user.
        $isCompleted = $this->model->toggleTask($this->get("PARAMS.id"));
        // Receive feedback on the front end ajax
        echo $isCompleted;
    }

    /**
     * Delete a task.
     */
    public function deleteTask()
    {
        // Add validation that the id belongs to the list that belongs to the user.
        $this->model->deleteById($this->get("PARAMS.id"));
        // Receive feedback on the front end ajax
        echo 1;
    }
    
    /**
     * Validate if the POST form is valid.
     * content and list_id validation.
     */
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