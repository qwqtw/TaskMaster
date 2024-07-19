<?php

class Task extends Model
{
    public function __construct()
    {
        parent::__construct("task");
    }

    public function getTasks($listId)
    {
        $this->load(["list_id = ?", $listId]);
        return $this->query;
    }

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

    public function toggleTask($id)
    {
        $this->load(["id = ?", $id]);

        $this->is_completed = !$this->is_completed;

        $this->update();
    }
}