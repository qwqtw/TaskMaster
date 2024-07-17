<?php

class Lists extends Model
{
    public function __construct()
    {
        parent::__construct("list");
    }

    public function getListByName($title)
    {
        return $this->findone(["title = ?", $title]);
    }

/**
     * Create the list entry and.
     * @return int id of the newly created list
     */
    public function createList()
    {
        $this->copyfrom("POST");
        $this->list_order = 0;
        
        $this->save();
        return $this->id;
    }
}