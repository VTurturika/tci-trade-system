<?php

class CategoryModel extends Model
{

    public function get($params, $id)
    {
        Logger::logWithMsg("id ", $id);
        if ($id == null || $id == 0) {
            $id = 0;
            $query = $this->db->table("Category")->select("*")->where('parent', '=', $id);
            return $query->get();
        } else {
            $query = $this->db->table("Category")->select("*")->where('id', '=', $id)->get();
            Logger::logWithMsg("cat ", $query);
            if (sizeof($query) < 1) return array();
            $char = $this->db->query("SELECT * FROM Characteristic c JOIN (SELECT id FROM Category WHERE parent=".$id
                .") k ON c.id = k.id")->get();
            Logger::logWithMsg("char ", $char);
            $query["characteristics"] = $char;
            Logger::logWithMsg("cat ", $query);
            return $query;
        }
    }

    public function create($params, $args)
    {
        if ($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if (array_key_exists("name", $params) && array_key_exists("parent", $params)) {
            $data = array();
            $data["name"] = $params["name"];
            $data["parent"] = $params["parent"];

            $parent = $this->get(null, $data["parent"]);
            if (sizeof($parent) < 1) return array();
            Logger::logWithMsg("parent ", $parent);
            Logger::logWithMsg("data ", $data);
            $create = $this->db->table("Category")->insert($data);
            Logger::logWithMsg("create ", $create);
            $res = $this->get(null, $create);
            Logger::logWithMsg("createRes ", $create);
            return $res;
        }

        return array();
    }

    public function modify($params, $args)
    {
        if ($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if ($args["id"] == null) return array();

        $data = array();
        if (array_key_exists("name", $params)) $data["name"] = $params["name"];
        if (array_key_exists("parent", $params)) $data["parent"] = $params["parent"];
//        if (array_key_exists("children", $params)) $data["children"] = $params["children"];
//        if (array_key_exists("characteristics", $params)) $data["characteristics"] = $params["characteristics"];
        Logger::logWithMsg("data ", $data);
        $this->db->table("Category")->where('id', $args["id"])->update($data);
        return $this->get(null, $args["id"]);

    }

    public function delete($params, $id)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if($id == null) return array();
        $obj = $this->get(null, $id);
        Logger::logWithMsg("objToDel ", $obj);

        $params = array();
        $params["parent"] = $id - 1;
        Logger::logWithMsg("params ", $params);
        foreach ($obj["children"] as $c) {
            Logger::logWithMsg("idToUpdate ", $c);
            $this->modify($params, $c);
        }

        $this->db->table("Category")->where('id', $id)->delete();
        return $obj;
    }
}