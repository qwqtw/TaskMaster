<?php

class Task extends Model
{
    private $byPriority = false;
    private $byDueDate = false;
    
    
    public function __construct()
    {
        parent::__construct("task");
    }

    /**
     * Set the options for filtering getTasks
     */
    public function setOptions($byPriority, $byDueDate)
    {
        $this->byPriority = $byPriority;
        $this->byDueDate = $byDueDate;
    }

    /**
     * Create a task
     * @return int the new task id
     */
    public function create()
    {
        $this->copyPOST();

        $this->save();
        return $this->id;
    }

    /**
     * Update the task
     * @return object the updated task object
     */
    public function updateTask($id)
    {
        $this->load(["id = ?", $id]);
        $this->copyPOST();
        
        $this->update();
        return $this;
    }

    /**
     * Get all tasks for the given $listId.
     * @param int $listId the given list id
     * @return Object the query results
     */
    public function getTasksAll($listId)
    {
        return $this->getTasks($listId, "list_id = ?");
    }

    /**
     * Get active tasks for the given $listId.
     * @param int $listId the given list id
     * @return Object the query results
     */
    public function getTasksActive($listId)
    {
        return $this->getTasks($listId, "list_id = ? AND is_completed = 0");
    }

    /**
     * Get completed tasks for the given $listId.
     * @param int $listId the given list id
     * @param bool $byPriority order by priority
     * @return object the query results
     */
    public function getTasksCompleted($listId)
    {
        return $this->getTasks($listId, "list_id = ? AND is_completed = 1");
    }

    /**
     * Get the number of active tasks for the list.
     * @param int $listId the given list id
     * @return int the active task count for the list
     */
    public function countTasksActive($listId)
    {
        return $this->count(["list_id = ? AND is_completed = 0", $listId]);
    }

    /**
     * Toggle the task's is_completed state.
     * @return bool the task is_completed value;
     */
    public function toggleTask($id)
    {
        $this->load(["id = ?", $id]);
        $this->is_completed = !$this->is_completed;

        $this->update();
        return $this->is_completed;
    }

    /**
     * Delete the tasks that belong to a list.
     * @param int $listId the list id containing the tasks
     * @return bool if the delete was succesful
     * @
     */
    public function deleteTaskByList($listId)
    {
        $this->load(["list_id = ?", $listId]);
        $isDeleted = $this->db->exec("DELETE FROM task WHERE list_id = ?", $listId);

        return ($this->dry() || $isDeleted > 0);
    }

    /**
     * Get tasks based on the sql statement and 
     * ordered by priority if requested.
     * @param int $listId the given list id
     * @param string $sql the filter string
     * @return object the query results
     */
    private function getTasks($listId, $sql)
    {
        $options = [];

        if ($this->byPriority) {
            array_push($options, "priority DESC");
        }
        if ($this->byDueDate) {
            array_push($options, "due_date DESC");
        }
        // Create the order by string
        $sqlOptions = (!empty($options)) ? "ORDER BY " . implode(", ", $options) : "";

        $this->load([$sql . " " . $sqlOptions, $listId]);
        return $this->query;
    }

    /**
     * Copy from POST and convert empty optional fields to null.
     */
    private function copyPOST()
    {
        $this->copyfrom("POST");

        if ($this->due_date == "") {
            $this->due_date = null;
        }
        if ($this->priority == "") {
            $this->priority = null;
        }
    }
}