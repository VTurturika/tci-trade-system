<?php

class TransactionModel extends Model {

    public function create($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);


        if (array_key_exists("type", $params) &&
            array_key_exists("document", $params) &&
            array_key_exists("date", $params) &&
            array_key_exists("is_conducted", $params) &&
            array_key_exists("counterparty", $params) &&
            array_key_exists("instances", $params) &&
            count($params["instances"]) > 0
        ) {
            return $params;
        }
        else return array();
    }
}