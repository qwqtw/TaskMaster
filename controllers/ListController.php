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
            "user_id" => $_SESSION["userId"],
        ]);

        if ($this->isFormValid()) {

            $listId = $this->model->create();
            $this->f3->reroute("@appList(@id={$listId})");
        }
        $this->f3->reroute("@app");
    }

    public function editTitle()
    {
        $listId = $_SESSION["listId"];

        $this->set("POST", [
            "title" => trim($this->get("POST.title")),
        ]);

        // If the form is valid, try to update, it will return the title or 0
        echo ($this->isFormValid()) ? $this->model->updateTitle($listId) : 0;
    }

    /**
     * Delete a list and its tasks.
     */
    public function delete()
    {
        $listId = $this->get("PARAMS.id");
        $isTaskDeleted = $this->task->deleteTaskByList($listId);
        $isListDeleted = false;

        if ($isTaskDeleted) {
            $isListDeleted = $this->model->delete($listId);

            // Make sure the selected list is unset if it was deleted
            if ($isListDeleted && ($_SESSION["listId"] === $listId)) {
                $_SESSION["listId"] = null;
            }
        }
        echo ($isTaskDeleted && $isListDeleted);
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