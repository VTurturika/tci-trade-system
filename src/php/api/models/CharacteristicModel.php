<?php

class CharacteristicModel extends Model {

    public function get($params, $id)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if($id != null) $charasteristicQuty = $this->db->table("Characteristic")->select("*")->where('id', '=', $id);
        else $charasteristicQuty = $this->db->table("Characteristic")->select("*");

        return $charasteristicQuty->get();
    }

    public function create($params, $args)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if(array_key_exists("name", $params) && array_key_exists("type", $params)) {
            $data = array();
            $data["name"] = $params["name"];
            $data["type"] = $params["type"];

            Logger::logWithMsg("data ", $data);
            return $this->get(null, $this->db->table("Characteristic")->insert($data));
        }

        return array();

    }

    public function modify($params, $args)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if($args["id"] == null) return array();

        if(array_key_exists("name", $params) && array_key_exists("type", $params)) {
            $data = array();
            $data["name"] = $params["name"];
            $data["type"] = $params["type"];
            Logger::logWithMsg("data ", $data);
            $this->db->table("Characteristic")->where('id', $args["id"])->update($data);
            return $this->get(null, $args["id"]);
        }

    }

    public function delete($params, $id)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);

        if($id == null) return array();
        $toReturn = $this->get(null, $id);
        $this->db->table("Characteristic")->where('id', $id)->delete();
        return $toReturn;
    }
}