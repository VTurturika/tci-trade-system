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
            $isConducted = boolval($params["is_conducted"]);
            $isSelling = boolval($params["type"]);

            Logger::logWithMsg("isConducted", $isConducted);
            Logger::logWithMsg("isSelling", $isSelling);

            $globalData = $this->db->table("Data")->select("*")->get()[0];
            $transaction = array(
                "type" => $params["type"] == "true" ? 1 : 0,
                "total_count" => 0,
                "total_price" => 0.0,
                "document" => $params["document"],
                "preparing_date" => $isConducted ? null : $params["date"],
                "conducted_date" => $isConducted ? $params["date"] : null,
                "balance_before" =>  floatval($globalData->balance),
                "balance_after" => 0,
                "index" => $isConducted ? $globalData->transaction_index + 1 : null
            );
            $transactionId = $this->db->table("Transaction")->insert($transaction);

            foreach ($params["instances"] as $i) {

                $instance = $this->db->table("Instance")
                    ->select("*")
                    ->where("product", "=", $i["product"])
                    ->where("buying_price", "=", $i["price"])
                    ->get();

                if(count($instance) == 0) {

                    $instance = array(
                        "product" => $i["product"],
                        "buying_price" => $i["price"],
                        "buying_count" => $i["count"],
                        "current_count" => $i["count"],
                        "currency" => $i["currency"],
                        "storage" => $i["storage"]
                    );
                    $instance["id"] = $this->db->table("Instance")->insert($instance);
                    $instanceId = $instance["id"];
                }
                else {
                    Logger::log($instance);
                    $instance = $instance[0];
                    $instanceId = $instance->id;
                }

                $instanceTransaction = array(
                    "instance" => $instanceId,
                    "transaction" => $transactionId,
                    "counterparty" => $params["counterparty"],
                    "type" => $isSelling ? 1 : 0,
                    "selling_price" =>  $isSelling ? $i["price"] : null,
                    "selling_count" => $isSelling ? $i["count"] : null
                );
                $count = $i["count"];
                $this->db->table("Instance_Transaction")->insert($instanceTransaction);
                if($isSelling) {
                    //todo add check negative current_count
                    $this->db->query("UPDATE Instance SET current_count = current_count - $count " .
                                     "WHERE id=$instanceId;");
                }
                $transaction["total_count"]++;
                $transaction["total_price"] += $i["count"] * $i["price"];
            }

            //todo check negative balance
            $transaction["balance_after"] = $globalData->balance +
                $isSelling ? +$transaction["total_price"] : -$transaction["total_price"];

            $this->db->table("Transaction")->where("id", "=", $transactionId)->update($transaction);
            $this->db->table("Data")->update(array(
                "balance" => $transaction["balance_after"],
                "transaction_index" => $isConducted
                    ? $globalData->transaction_index + 1
                    : $globalData->transaction_index
            ));

            return $transaction;
        }
        else return array("msg" => "type, document, date, is_conducted, counterparty, instances are required");
    }
}