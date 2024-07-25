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
        $this->load(["user_id = ? ORDER BY list_order", $_SESSION["userId"]]);
        return $this->query;
    }

    /**
     * Create the list entry.
     * @return int id of the newly created list
     */
    public function create()
    {
        $this->copyfrom("POST");
        // Count based on total list the user has
        $this->list_order = $this->count(["user_id = ?", $_SESSION["userId"]]);
        
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

    /**
     * Update the list title.
     * @param int $id the list id
     * @return string the title
     */
    public function updateTitle($id)
    {
        $this->load(["id = ?", $id]);
        $this->copyfrom("POST");

        $this->update();
        return $this->title;
    }

    public function updateListOrder($id, $newOrder)
    {
        $this->load(["id = ?", $id]);
        $currentOrder = $this->list_order;

        // Solution based on 
        // https://dba.stackexchange.com/questions/36875/arbitrarily-ordering-records-in-a-table
        $this->db->exec("UPDATE list SET list_order = list_order + 1 WHERE list_order >= ? AND list_order <= ?", [$newOrder, $currentOrder]);

        $this->load(["id = ?", $id]);
        $this->list_order = $newOrder;

        $this->update();
    }

    /**
     * Delete the list entry. Make sure tasks are deleted first.
     * @param int $id the list to delete
     */
    public function delete($id)
    {
        $this->load(["id = ?", $id]);
        return $this->erase();
    }
}