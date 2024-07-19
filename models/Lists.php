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

    public function getFirstList()
    {
        return $this->findone(["user_id = ?", $_COOKIE["user_id"]]);
    }

    public function getAll()
    {
        $this->load(["user_id = ?", $_COOKIE["user_id"]]);
        return $this->query;
    }

    /**
     * Create the list entry and.
     * @return int id of the newly created list
     */
    public function create()
    {
        $this->copyfrom("POST");
        $this->list_order = 0;
        
        $this->save();
        return $this->id;
    }
}