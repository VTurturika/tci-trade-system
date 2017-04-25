<?php

class ProductModel extends Model {

    public function get($params, $id) {

        if($params == null) $params = array();

        Logger::logWithMsg("params", $params);

        $characteristicQuery = $this->db->table("Characteristic")->select("*");
        $characteristicSubQuery = null;

        if(array_key_exists("characteristics", $params) && count($params["characteristics"]) > 0) {

            foreach ($params["characteristics"] as $c) {
                $characteristicQuery = $characteristicQuery->orWhere("id", $c["id"]);
            }

            $fullCharacteristics = $characteristicQuery->get();
            $characteristicsById = array();

            Logger::logWithMsg("fullCharacteristics", $fullCharacteristics);
            Logger::logWithMsg("requestCharacteristics", $params["characteristics"]);

            foreach ($fullCharacteristics as $c) {
                $characteristicsById[$c->id] = array("type" => $c->type,
                    "name" => $c->name, "measurements" => $c->measurements
                );
            }

            Logger::logWithMsg("characteristicsById", $characteristicsById);
            $characteristicSubQuery = $this->db->table("Product_Characteristic")->select("*");

            foreach ($params["characteristics"] as $c) {

                if(array_key_exists("valueFrom", $c) && array_key_exists("valueTo", $c)) {

                    $valueFrom = $characteristicsById[$c["id"]]->type == "integer" ? intval($c["valueFrom"])
                        : $characteristicsById[$c["id"]]->type == "float" ? floatval($c["valueFrom"])
                        : $c["valueFrom"];

                    $valueTo = $characteristicsById[$c["id"]]->type == "integer" ? intval($c["valueTo"])
                        : $characteristicsById[$c["id"]]->type == "float" ? floatval($c["valueTo"])
                        : $c["valueTo"];

                    $characteristicSubQuery = $characteristicSubQuery
                        ->where("characteristic", $c["id"])
                        ->whereBetween("value", $valueFrom, $valueTo);
                }
                else if (array_key_exists("value", $c)) {

                    $value = $characteristicsById[$c["id"]]->type=="integer" ? intval($c["value"])
                        : $characteristicsById[$c["id"]]->type=="float" ? floatval($c["value"])
                        : $c["value"];

                    $characteristicSubQuery = $characteristicSubQuery
                        ->where("characteristic", $c["id"])
                        ->where("value", $value);
                }
            }
            Logger::log($characteristicSubQuery->getQuery()->getRawSql());
        }

        $query = $this->db->table("Product")->select("*");

        if(array_key_exists("title", $params))
            $query = $query->where("title", $params["title"]);
        if(array_key_exists("category", $params))
            $query = $query->where("category", $params["category"]);
        if(array_key_exists("article", $params))
            $query = $query->where("article", $params["article"]);
        if(array_key_exists("barcode", $params))
            $query = $query->where("barcode", $params["barcode"]);
        if(array_key_exists("consignment", $params))
            $query = $query->where("consignment", $params["consignment"]);
        if(array_key_exists("manufacturer", $params))
            $query = $query->where("manufacturer", $params["manufacturer"]);
        if(array_key_exists("model", $params))
            $query = $query->where("model", $params["model"]);
        if(array_key_exists("series", $params))
            $query = $query->where("series", $params["series"]);
        if(array_key_exists("specification", $params))
            $query = $query->where("specification", $params["specification"]);

        return $query->get();
    }
}
