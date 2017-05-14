<?php

class TransactionModel extends Model {

    public function get($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        $select = "SELECT t.id, t.type, t.document, t.preparing_date, t.conducted_date, t.balance_before, " .
            "t.`index` as transaction_index, t.total_count, t.total_price, t.balance_after, " .
            "p.id as product, p.title, p.article, " .
            "i.id as instance, i.price, i.currency, i.storage, " .
            "it.buying_count, it.selling_count, it.selling_price, it.counterparty " .
            "FROM Instance_Transaction it";

        $instanceJoin = "JOIN Instance i ON it.instance = i.id ";
        if(array_key_exists("currency", $params)) {

            $currency = $params["currency"];
            $instanceJoin .= "AND i.currency = '$currency' ";
        }

        $transactionJoin = "JOIN Transaction t ON it.transaction = t.id ";
        if(array_key_exists("date", $params) &&
           array_key_exists("from", $params["date"]) &&
           array_key_exists("to", $params["date"])
        ) {
            $from = $params["date"]["from"];
            $to = $params["date"]["to"];
            $transactionJoin .= "AND t.conducted_date BETWEEN '$from' AND '$to' ";
        }
        if(array_key_exists("price", $params) &&
           array_key_exists("from", $params["price"]) &&
           array_key_exists("to", $params["price"])
        ) {
            $from = floatval($params["price"]["from"]);
            $to = floatval($params["price"]["to"]);
            $transactionJoin .= "AND t.total_price BETWEEN $from AND $to ";
        }
        if(array_key_exists("is_conducted", $params)) {
            $isConducted = boolval($params["is_conducted"]);
            $transactionJoin .= "AND t.conducted_date " . $isConducted ? " = " : " != " . "NULL ";
        }

        $productJoin = "JOIN Product p ON i.product = p.id ";
        if(array_key_exists("products", $params) && count($params["products"]) > 0) {

            $specifiedProducts = "JOIN (SELECT id FROM Instance WHERE ";
            for( $i=0; $i < count($params["products"]); $i++ ) {

                $p = $params["products"][$i];
                $specifiedProducts .= ($i == 0) ? " product = $p " : "OR product = $p ";
            }

            $specifiedProducts .= ") pi on pi.id = it.instance ";

            $productJoin .= $specifiedProducts;
        }

        $where = "WHERE true ";
        if(array_key_exists("type", $params)) {

            $type = $params["type"];
            $where .= "AND t.type = $type ";
        }
        if(array_key_exists("counterparty", $params)) {

            $counterparty = intval($params["counterparty"]);
            $where .= "AND it.counterparty = $counterparty ";
        }

        $fullQuery = "$select $instanceJoin $transactionJoin $productJoin $where ".
            "ORDER BY transaction_index DESC;";
        Logger::logWithMsg("fullQuery", $fullQuery);

        $dbResults = $this->db->query($fullQuery)->get();
        $result = array(
            "balance_before" => 0.0,
            "balance_after" => 0.0,
            "transactions" => array()
        );

        foreach ($dbResults as $t) {

            if(!array_key_exists($t->id, $result["transactions"])) {

                $result["transactions"][$t->id] = array(
                    "id" => intval($t->id),
                    "type" => $t->type,
                    "document" => $t->document,
                    "preparing_date" => $t->preparing_date,
                    "conducted_date" => $t->conducted_date,
                    "balance_before" => floatval($t->balance_before),
                    "balance_after" => floatval($t->balance_after),
                    "transaction_index" => intval($t->transaction_index),
                    "counterparty" => "",
                    "total_count" => intval($t->total_count),
                    "total_price" => floatval($t->total_price),
                    "products" => array()
                );
            }

            if (!array_key_exists($t->product, $result["transactions"][$t->id]["products"])
            ) {

                $result["transactions"][$t->id]["products"][$t->product] = array(
                    "id" => intval($t->product),
                    "title" => $t->title,
                    "article" => $t->article,
                    "instances" => array()
                );
            }

            if (!array_key_exists($t->instance,
                $result["transactions"][$t->id]["products"][$t->product]["instances"])
            ) {

                $result["transactions"][$t->id]["products"][$t->product]["instances"][$t->instance] = array(
                    "id" => intval($t->instance),
                    "count" => $t->type == 0 ? intval($t->buying_count) : intval($t->selling_count),
                    "price" => $t->type == 0 ? floatval($t->price) : floatval($t->selling_price),
                    "currency" => $t->currency,
                    "storage" => $t->storage
                );
                $result["transactions"][$t->id]["counterparty"] = intval($t->counterparty);
            }
        }

        $result["transactions"] = array_values($result["transactions"]);
        foreach ($result["transactions"] as $transaction=>$t) {

            $result["transactions"][$transaction]["products"] =
                array_values($result["transactions"][$transaction]["products"]);
            foreach ($result["transactions"][$transaction]["products"] as $product=>$p) {
                $result["transactions"][$transaction]["products"][$product]["instances"] =
                    array_values($result["transactions"][$transaction]["products"][$product]["instances"]);
            }
        }

        $result["balance_before"] = $result["transactions"][count($result["transactions"]) - 1]["balance_before"];
        $result["balance_after"] = $result["transactions"][0]["balance_after"];

        return $result;
    }

    public function buy($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        if (array_key_exists("document", $params) &&
            array_key_exists("date", $params) &&
            array_key_exists("is_conducted", $params) &&
            array_key_exists("counterparty", $params) &&
            array_key_exists("instances", $params) &&
            count($params["instances"]) > 0
        ) {
            $isConducted = boolval($params["is_conducted"]);

            $globalData = $this->db->table("Data")->select("*")->get()[0];
            $transaction = array(
                "type" => 0,
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
                    ->where("price", "=", $i["price"])
                    ->get();

                $count = $i["count"];
                if(count($instance) == 0) {

                    $instance = array(
                        "product" => $i["product"],
                        "price" => $i["price"],
                        "count" => $i["count"],
                        "currency" => $i["currency"],
                        "storage" => $i["storage"]
                    );
                    $instance["id"] = $this->db->table("Instance")->insert($instance);
                    $instanceId = $instance["id"];
                }
                else {
                    $instance = $instance[0];
                    $instanceId = $instance->id;
                    $this->db->query("UPDATE Instance SET count = count + $count ".
                        "WHERE id=$instanceId");
                }

                $instanceTransaction = array(
                    "instance" => $instanceId,
                    "transaction" => $transactionId,
                    "counterparty" => $params["counterparty"],
                    "type" => 0,
                    "selling_price" => null,
                    "selling_count" => null,
                    "buying_count" => $i["count"]
                );
                $this->db->table("Instance_Transaction")->insert($instanceTransaction);

                $transaction["total_count"]++;
                $transaction["total_price"] += $i["count"] * $i["price"];
            }

            //todo check negative balance
            $transaction["balance_after"] = $globalData->balance - $transaction["total_price"];

            $this->db->table("Transaction")->where("id", "=", $transactionId)->update($transaction);
            $this->db->table("Data")->update(array(
                "balance" => $transaction["balance_after"],
                "transaction_index" => $isConducted
                    ? $globalData->transaction_index + 1
                    : $globalData->transaction_index
            ));
            $transaction["id"] = intval($transactionId);

            return $this->generateJson($transaction);
        }
        else return array("msg" => "type, document, date, is_conducted, counterparty, instances are required");
    }

    public function sell($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        if (array_key_exists("document", $params) &&
            array_key_exists("date", $params) &&
            array_key_exists("is_conducted", $params) &&
            array_key_exists("counterparty", $params) &&
            array_key_exists("instances", $params) &&
            count($params["instances"]) > 0
        ) {
            $isConducted = boolval($params["is_conducted"]);

            $globalData = $this->db->table("Data")->select("*")->get()[0];
            $transaction = array(
                "type" => 1,
                "total_count" => 0,
                "total_price" => 0.0,
                "document" => $params["document"],
                "preparing_date" => $isConducted ? null : $params["date"],
                "conducted_date" => $isConducted ? $params["date"] : null,
                "balance_before" => floatval($globalData->balance),
                "balance_after" => 0,
                "index" => $isConducted ? $globalData->transaction_index + 1 : null
            );
            $transactionId = $this->db->table("Transaction")->insert($transaction);
            foreach ($params["instances"] as $i) {

                $count = $i["count"];
                $id = $i["id"];
                $instanceTransaction = array(
                    "instance" => $i["id"],
                    "transaction" => $transactionId,
                    "counterparty" => $params["counterparty"],
                    "type" => 1,
                    "selling_price" => $i["price"],
                    "selling_count" => $i["count"],
                    "buying_count" => null
                );
                $this->db->table("Instance_Transaction")->insert($instanceTransaction);
                $this->db->query("UPDATE Instance SET count = count - $count WHERE id = $id;");

                $transaction["total_count"]++;
                $transaction["total_price"] += $i["count"] * $i["price"];
            }
            $transaction["balance_after"] = $globalData->balance + $transaction["total_price"];

            $this->db->table("Transaction")->where("id", "=", $transactionId)->update($transaction);
            $this->db->table("Data")->update(array(
                "balance" => $transaction["balance_after"],
                "transaction_index" => $isConducted
                    ? $globalData->transaction_index + 1
                    : $globalData->transaction_index
            ));
            $transaction["id"] = intval($transactionId);

            return $this->generateJson($transaction);
        }
        else return array("msg" => "type, document, date, is_conducted, counterparty, instances are required");
    }

    public function conduct($params, $args) {

        if($params == null) $params = array();
        if($args == null) $args = array();

        Logger::logWithMsg("Request body", $params);
        Logger::logWithMsg("Arguments", $args);

        if(array_key_exists("date", $params) && array_key_exists("id", $args)) {

            $globalData = $this->db->table("Data")->select("*")->get()[0];
            $transaction = array(
                "conducted_date" => $params["date"],
                "index" => $globalData->transaction_index + 1
            );

            $this->db->table("Transaction")->where("id", "=", $args["id"])->update($transaction);
            $this->db->table("Data")->update(array("transaction_index" => $globalData->transaction_index + 1 ));

            $transactionFromDb = $this->db->table("Transaction")
                ->select("*")
                ->where("id", "=", $args["id"])
                ->get()[0];

            $transaction["id"] = intval($transactionFromDb->id);
            $transaction["type"] = $transactionFromDb->type;
            $transaction["document"] = $transactionFromDb->document;
            $transaction["preparing_date"] = $transactionFromDb->preparing_date;
            $transaction["total_count"] = $transactionFromDb->total_count;
            $transaction["total_price"] = $transactionFromDb->total_price;
            $transaction["balance_before"] = $transactionFromDb->balance_before;
            $transaction["balance_after"] = $transactionFromDb->balance_after;
            return $this->generateJson($transaction);
        }
        else return array("msg" => "date and id are required");
    }

    private function generateJson($transaction) {

        Logger::logWithMsg("transaction", $transaction);
        $transaction["transaction_index"] = $transaction["index"];
        unset($transaction["index"]);
        $transaction["products"] = array();

        $products = $this->db->query("SELECT * FROM Instance i JOIN Instance_Transaction it " .
                                     "ON i.id = it.instance AND it.transaction = " . $transaction["id"])->get();

        Logger::logWithMsg("products", $products);

        foreach ($products as $row) {

            if(!array_key_exists($row->product, $transaction["products"])) {

                $transaction["products"][$row->product] = array(
                    "id" => $row->product,
                    "instances" => array()
                );
                $transaction["counterparty"] = $row->counterparty;
            }

            if(!array_key_exists($row->instance, $transaction["products"][$row->product]["instances"])) {

                $transaction["products"][$row->product]["instances"][$row->instance] = array(
                    "id" => $row->instance,
                    "count" => $row->count,
                    "price" => $row->price,
                    "currency" => $row->currency,
                    "storage" => $row->storage
                );
            }
        }

        $transaction["products"] = array_values($transaction["products"]);
        foreach ($transaction["products"] as $key=>$p) {
            $result = array();
            foreach ($p["instances"] as $i) {
                array_push($result, $i);
            }
            Logger::logWithMsg("Result", $result);
            $transaction["products"][$key]["instances"] = $result;
        }

        Logger::logWithMsg("transaction", $transaction);

        return $transaction;
    }
}