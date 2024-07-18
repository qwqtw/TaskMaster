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
}