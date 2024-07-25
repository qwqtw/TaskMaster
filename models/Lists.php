<?php

class Lists extends Model
{
    public function __construct()
    {
        parent::__construct("list");
    }

    /**
     * Get a list by it's name.
     * @param string $title the list title
     * @return object the list if found
     */
    public function getListByName($title)
    {
        return $this->findone(["title = ?", $title]);
    }

    /**
     * Get the first list for the user.
     * @return object the first list found based on list_order
     */
    public function getFirstList()
    {
        return $this->findone(["user_id = ? ORDER BY list_order", $_SESSION["userId"]]);
    }

    /**
     * Get all lists by list_order for the user.
     * @return array returned list that matched the criterias
     */
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

    /**
     * Update the order of the lists.
     * @param int $id the list id that changed
     * @param int $newOrder the list's new position
     */
    public function updateListOrder($id, $newOrder)
    {
        $this->load(["id = ?", $id]);
        $currentOrder = $this->list_order;

        // Solution based on 
        // https://dba.stackexchange.com/questions/36875/arbitrarily-ordering-records-in-a-table
        $filters = ($newOrder > $currentOrder) ? [-1, $currentOrder, $newOrder] : [1, $newOrder, $currentOrder];
        $this->db->exec("UPDATE list SET list_order = list_order + ? WHERE list_order >= ? AND list_order <= ?", $filters);

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
