<?php

class Task extends Model
{
    public function __construct()
    {
        parent::__construct("task");
    }

    // TODO: docs
    public function create()
    {
        $this->copyfrom("POST");

        if ($this->due_date == "") {
            $this->due_date = null;
        }
        if ($this->priority == "") {
            $this->priority = null;
        }

        $this->save();
        return $this->id;
    }

    /**
     * Get all tasks for the given $listId.
     * @param int $listId the given list id
     * @param bool $byPriority order by priority
     * @return Object the query results
     */
    public function getTasksAll($listId, $byPriority = false)
    {
        $sql = "list_id = ?";
        return $this->getTasks($listId, $sql, $byPriority);
    }

    /**
     * Get active tasks for the given $listId.
     * @param int $listId the given list id
     * @param bool $byPriority order by priority
     * @return Object the query results
     */
    public function getTasksActive($listId, $byPriority = false)
    {
        $sql = "list_id = ? AND is_completed = 0";
        return $this->getTasks($listId, $sql, $byPriority);
    }

    /**
     * Get completed tasks for the given $listId.
     * @param int $listId the given list id
     * @param bool $byPriority order by priority
     * @return Object the query results
     */
    public function getTasksCompleted($listId, $byPriority = false)
    {
        $sql = "list_id = ? AND is_completed = 1";
        return $this->getTasks($listId, $sql, $byPriority);
    }

    // TODO: docs
    public function toggleTask($id)
    {
        $this->load(["id = ?", $id]);

        $this->isCompleted = !$this->isCompleted;

        $this->update();
        
        return $this->isCompleted;
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
    private function getTasks($listId, $sql, $byPriority = false)
    {
        $sqlPriority = ($byPriority) ? "ORDER BY priority DESC" : "";

        $this->load([$sql . " " . $sqlPriority, $listId]);
        return $this->query;
    }
}