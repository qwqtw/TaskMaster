<?php

class Task extends Model
{
    private $byPriority = false;
    private $byDueDate = false;
    
    
    public function __construct()
    {
        parent::__construct("task");
    }

    public function setOptions($byPriority, $byDueDate)
    {
        $this->byPriority = $byPriority;
        $this->byDueDate = $byDueDate;
    }

    // TODO: docs
    public function create()
    {
        $this->copyPOST();

        $this->save();
        return $this->id;
    }

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
        $sql = "list_id = ?";
        return $this->getTasks($listId, $sql);
    }

    /**
     * Get active tasks for the given $listId.
     * @param int $listId the given list id
     * @return Object the query results
     */
    public function getTasksActive($listId)
    {
        $sql = "list_id = ? AND is_completed = 0";
        return $this->getTasks($listId, $sql);
    }

    /**
     * Get completed tasks for the given $listId.
     * @param int $listId the given list id
     * @param bool $byPriority order by priority
     * @return Object the query results
     */
    public function getTasksCompleted($listId)
    {
        $sql = "list_id = ? AND is_completed = 1";
        return $this->getTasks($listId, $sql);
    }

    // TODO: docs
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
     * @param bool $byPriority order by priority
     * @return Object the query results
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

        $optionsStr = implode(", ", $options);
        $sqlOptions = (!empty($options)) ? "ORDER BY " . $optionsStr : "";

        $this->load([$sql . " " . $sqlOptions, $listId]);
        return $this->query;
    }

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