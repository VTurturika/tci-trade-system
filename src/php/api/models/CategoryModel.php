<?php

class CategoryModel extends Model {

    public function get() {

        return $this->db->table("Category")->select("*")->get();
    }
}