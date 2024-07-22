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
        return $this->findone(["user_id = ?", $_SESSION["userId"]]);
    }

    public function getAll()
    {
        $this->load(["user_id = ?", $_SESSION["userId"]]);
        return $this->query;
    }

    /**
     * Create the list entry.
     * @return int id of the newly created list
     */
    public function create()
    {
        $this->copyfrom("POST");
        $this->list_order = 0;
        
        $this->save();
        return $this->id;
    }
    /**
     * Create the default list.
     * @return int id of the newly created list.
     */
    public function createDefault()
    {
        // Create a default list
        $this->copyfrom([
            "title" => "To Do",
            "user_id" => $_SESSION["userId"],
            "list_order" => 0,
        ]);

        $this->save();
        return $this->id;
    }

    public function updateTitle($id)
    {
        $this->load(["id = ?", $id]);
        $this->copyfrom("POST");

        $this->update();
    }

    /**
     * Delete the list entry. Make sure tasks are deleted first.
     * @param int $id the list to delete
     */
    public function delete($id)
    {
        $this->load(["id = ?", $id]);
        $this->erase();
    }
}