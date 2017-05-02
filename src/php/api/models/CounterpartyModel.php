<?php

class CounterpartyModel extends Model {

    public function get($params, $id)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);
        if($id != null) $counterpartyQuty = $this->db->table("Counterparty")->select("*")->where('id', '=', $id);
        else $counterpartyQuty = $this->db->table("Counterparty")->select("*");
        return $counterpartyQuty->get();
    }
    public function create($params, $args)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);
        if(array_key_exists("title", $params) && array_key_exists("type", $params) && array_key_exists("firstname", $params)
            && array_key_exists("lastname", $params)&& array_key_exists("middlename", $params)) {
            $data = array();
            $data["title"] = $params["title"];
            $data["type"] = $params["type"];
            $data["firstname"] = $params["firstname"];
            $data["lastname"] = $params["lastname"];
            $data["middlename"] = $params["middlename"];
            $data["phone"] = $params["phone"];
            $data["email"] = $params["email"];
            $data["address"] = $params["address"];
            $data["JP_type"] = $params["JP_type"];
            $data["JP_code"] = $params["JP_code"];
            $data["comment"] = $params["comment"];
            Logger::logWithMsg("data ", $data);
            return $this->get(null, $this->db->table("Counterparty")->insert($data));
        }
        return array();
    }
    public function modify($params, $args)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);
        if($args["id"] == null) return array();
            $data = array();
            if(array_key_exists("title", $params))
                $data["title"] = $params["title"];
            if(array_key_exists("type", $params))
                $data["type"] = $params["type"];
            if(array_key_exists("firstname", $params))
                $data["firstname"] = $params["firstname"];
            if(array_key_exists("lastname", $params))
                $data["lastname"] = $params["lastname"];
            if(array_key_exists("middlename", $params))
                $data["middlename"] = $params["middlename"];
            if(array_key_exists("phone", $params))
                $data["phone"] = $params["phone"];
            if(array_key_exists("email", $params))
                $data["email"] = $params["email"];
            if(array_key_exists("address", $params))
                $data["address"] = $params["address"];
            if(array_key_exists("JP_type", $params))
                $data["JP_type"] = $params["JP_type"];
            if(array_key_exists("JP_code", $params))
                $data["JP_code"] = $params["JP_code"];
            if(array_key_exists("comment", $params))
                $data["comment"] = $params["comment"];
            Logger::logWithMsg("data ", $data);
            $this->db->table("Counterparty")->where('id', $args["id"])->update($data);
            return $this->get(null, $args["id"]);
        }
    public function delete($params, $id)
    {
        if($params == null) $params = array();
        Logger::logWithMsg("parsedBody ", $params);
        if($id == null) return array();
        $toReturn = $this->get(null, $id);
        $this->db->table("Counterparty")->where('id', $id)->delete();
        return $toReturn;
    }

}
