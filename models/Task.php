<?php

class Task extends Model
{
    private $view;


    public function __construct()
    {
        parent::__construct("task");

        $this->view = new ViewUserTask();
    }

    /**
     * Get task by id and user_id, through the view
     * @param int $id the task id
     * @return object the task or false
     */
    public function getById($id)
    {
        return $this->view->getById($id);
    }

    /**
     * Create a task
     * @return int the new task id
     */
    public function create()
    {
        $this->copyFields();
        $this->save();

        return $this->id;
    }

    /**
     * Update the task
     * @throws Exception if id is invalid or not found
     * @return object the updated task object or false if failed to update
     */
    public function updateTask($id)
    {
        // Validate or throw exception
        $this->isValidId($id);

        $this->load(["id = ?", $id]);
        $this->copyFields();
        $this->update();

        return $this;
    }

    /**
     * Toggle the task's is_completed state.
     * @throws Exception if id is invalid or not found
     * @return bool the task is_completed value;
     */
    public function toggleTask($id)
    {
        // Validate or throw exception
        $this->isValidId($id);

        $this->load(["id = ?", $id]);
        $this->is_completed = !$this->is_completed;
        $this->update();

        return $this->is_completed;
    }

    /**
     * Delete a row from the table using id primary key and user id.
     * @param int id row to delete
     * @throws Exception if id is invalid or not found
     * @return bool success feedback
     */
    public function deleteById($id)
    {
        // Validate or throw exception
        $this->isValidId($id);

        $this->load(["id = ?", $id]);
        return $this->erase();
    }

    /**
     * Delete the tasks that belong to a list.
     * @param int $listId the list id containing the tasks
     * @throws Exception if the list id is invalid
     * @return bool if the delete was succesful
     */
    public function deleteTaskByList($listId)
    {
        // Validate the listId belongs to the user or throw an exception
        if (!$this->view->getByListId($listId)) {
            throw new Exception("Invalid list id");
        }

        $this->load(["list_id = ?", $listId]);
        $isDeleted = $this->db->exec("DELETE FROM task WHERE list_id = ?", $listId);

        return ($this->dry() || $isDeleted > 0);
    }

    /**
     * Copy from POST and convert empty optional fields to null.
     */
    private function copyFields()
    {
        $this->copyfrom("POST");

        if ($this->due_date == "") {
            $this->due_date = null;
        }
        if ($this->priority == "") {
            $this->priority = null;
        }
    }

    /**
     * Validate if the task id is found with the id and user id
     * @param $id the task id
     * @throws Exception if the id is not found
     */
    private function isValidId($id)
    {
        // Will throw an exception if not found or it doesn't belong.
        if (!$this->getById($id)) {
            throw new Exception("Invalid id");
        }
    }
}
