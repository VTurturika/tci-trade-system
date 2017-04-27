<?php

class ProductModel extends Model {

    public function get($params, $id) {

        if($params == null) $params = array();
        Logger::logWithMsg("params", $params);

        return ($id == null) ? $this->getFilteredProducts($params) : $this->getSpecifiedProduct($params, $id);
    }

    private function getFilteredProducts($params) {

        $mainSelect =
            "SELECT pc.product, p.title, p.category, p.article, p.barcode, p.consignment, p.manufacturer, "
            . "p.model, p.series, p.specification, pc.characteristic, c.name, c.type, c.measure, pc.value, "
            . "it.instance, i.current_count AS count, i.buying_count, i.buying_price, i.currency, "
            . "t.conducted_date AS date, it.counterparty, ct.title AS counterparty_title "
            . "FROM Product_Characteristic pc";

        $characteristicJoin = "JOIN Characteristic c ON c.id = pc.characteristic";
        $counterpartyJoin =  "JOIN Counterparty ct ON ct.id = it.counterparty ";

        $featuresJoin = "";
        if(array_key_exists("characteristics", $params) && count($params["characteristics"]) > 0) {

            $i = 1;
            foreach ($params["characteristics"] as $c) {

                $valueStr = "";

                if(array_key_exists("valueFrom", $c) && array_key_exists("valueTo", $c)) {

                    if($c["valueFrom"] == null and $c["valueTo"] != null) { // value <= valueTo

                        if ($c["type"] == "float")  $valueStr = "<= " . floatval($c["valueTo"]);
                        else if($c["type"] == "integer") $valueStr = "<= " . intval($c["valueTo"]);
                        else continue;
                    }
                    else if ($c["valueFrom"] != null and $c["valueTo"] == null) { // value >= valueFrom

                        if ($c["type"] == "float")  $valueStr = ">= " . floatval($c["valueFrom"]);
                        else if($c["type"] == "integer") $valueStr = ">= " . intval($c["valueFrom"]);
                        else continue;
                    }
                    else if ($c["valueFrom"] != null and $c["valueTo"] != null) {

                        if ($c["type"] == "float") {
                            $valueStr = "BETWEEN " . floatval($c["valueFrom"]) . " AND " . floatval($c["valueTo"]);
                        }
                        else if($c["type"] == "integer") {
                            $valueStr = "BETWEEN " . intval($c["valueFrom"]) . " AND " . intval($c["valueTo"]);
                        }
                        else continue;
                    }
                    else continue;
                }
                else if (array_key_exists("value", $c)) {

                    switch ($c["type"]) {

                        case "float":
                            $valueStr = "= " . floatval($c["value"]);
                            break;
                        case "integer":
                            $valueStr = "= " .intval($c["value"]);
                            break;
                        case "boolean":
                            $valueStr = "= " . $c["value"];
                            break;
                        case "string":
                            $valueStr = "= " . "'" . $c["value"] . "'";
                            break;
                        default:
                            continue;
                    }
                }
                else continue;

                $id = $c["id"];
                $featureSelect = ($i == 1)
                    ? "JOIN (SELECT f1.product AS ids FROM " .
                      "(SELECT product FROM Product_Characteristic WHERE characteristic=$id AND value $valueStr ) f1 "
                    : "JOIN (SELECT product FROM Product_Characteristic " .
                      "WHERE characteristic=$id AND value $valueStr ) f$i ON f1.product = f$i.product ";
                $featuresJoin .= $featureSelect;
                $i++;
            }

            if (strpos($featuresJoin, "f1.product = f2.product") !== false) {
                $featuresJoin .= ") products ON pc.product = products.ids";
            }
            else $featuresJoin = "";
        }
        //Logger::logWithMsg("featuresJoin", $featuresJoin);

        $productJoin = "JOIN Product p ON pc.product = p.id ";
        if(array_key_exists("title", $params)) {
            $title = $params["title"];
            $productJoin .= "AND p.title = '$title' ";
        }
        if(array_key_exists("category", $params)) {
            $category = $params["category"];
            $productJoin .= "AND p.category = $category ";
        }
        if(array_key_exists("article", $params)) {
            $article = $params["article"];
            $productJoin .= "AND p.article = '$article' ";
        }
        if(array_key_exists("barcode", $params)) {
            $barcode = $params["barcode"];
            $productJoin .= "AND p.barcode = '$barcode' ";
        }
        if(array_key_exists("consignment", $params)) {
            $consignment = $params["consignment"];
            $productJoin .=  "AND p.consignment = '$consignment' ";
        }
        if(array_key_exists("manufacturer", $params)) {
            $manufacturer = $params["manufacturer"];
            $productJoin .= "AND p.manufacturer = '$manufacturer' ";
        }
        if(array_key_exists("model", $params)) {
            $model = $params["model"];
            $productJoin .= "AND p.model = '$model' ";
        }
        if(array_key_exists("series", $params)) {
            $series = $params["series"];
            $productJoin .= "AND p.series = '$series' ";
        }
        if(array_key_exists("specification", $params)) {
            $specification = $params["specification"];
            $productJoin .= "AND p.specification = '$specification' ";
        }
        //Logger::logWithMsg("productJoin", $productJoin);

        $instanceJoin = "JOIN Instance i ON p.id = i.product ";
        if(array_key_exists("price", $params)) {

            if($params["price"]["from"] == null && $params["price"]["to"] != null) { //buying_price <= to
               $instanceJoin .= "AND i.buying_price <= " . floatval($params["price"]["to"]) . " ";
            }
            else if ($params["price"]["from"] != null && $params["price"]["to"] == null) { //buying_price >= from
                $instanceJoin .= "AND i.buying_price >= " . floatval($params["price"]["from"]) . " ";
            }
            else if ($params["price"]["from"] != null && $params["price"]["to"] != null) {
                $instanceJoin .= "AND i.buying_price BETWEEN "
                    . floatval($params["price"]["from"]) . " AND "
                    . floatval($params["price"]["to"]) . " ";
            }
        }
        if(array_key_exists("count", $params)) {


            if($params["count"]["from"] == null && $params["count"]["to"] != null) { //current_count <= to
                $instanceJoin .= "AND i.current_count <= " . floatval($params["count"]["to"]) . " ";
            }
            else if ($params["count"]["from"] != null && $params["count"]["to"] == null) { //buying_price >= from
                $instanceJoin .= "AND i.current_count >= " . floatval($params["count"]["from"]) . " ";
            }
            else if ($params["count"]["from"] != null && $params["count"]["to"] != null) {
                $instanceJoin .= "AND i.current_count BETWEEN " . floatval($params["count"]["from"])
                              . " AND " . floatval($params["count"]["to"]) . " ";
            }
        }
        if(array_key_exists("storage", $params)) {
            $storage = $params["storage"];
            $instanceJoin .= "AND i.storage = '$storage' ";
        }
        if(array_key_exists("currency", $params)) {
            $currency = $params["currency"];
            $instanceJoin .= "AND i.currency = '$currency' ";
        }
        //Logger::logWithMsg("instanceJoin", $instanceJoin);

        $instanceTransactionJoin = "JOIN Instance_Transaction it ON i.id = it.instance AND it.type = 0 ";

        if(array_key_exists("counterparty", $params)) {
            $counterparty = $params["counterparty"];
            $instanceTransactionJoin .= "AND it.counterparty = $counterparty ";
        }
        //Logger::logWithMsg("instanceTransactionJoin", $instanceTransactionJoin);

        $transactionJoin = "JOIN Transaction t ON t.id = it.transaction ";
        if(array_key_exists("data", $params)) {

            if($params["data"]["from"] == null && $params["data"]["to"] != null) { //data <= to
                $to = $params["data"]["to"];
                $transactionJoin .= "AND t.conducted_date <= '$to' ";
            }
            else if ($params["data"]["from"] != null && $params["data"]["to"] == null) { //data >= from
                $from = $params["data"]["from"];
                $transactionJoin .= "AND t.conducted_date >= '$from' ";
            }
            else if ($params["data"]["from"] != null && $params["data"]["to"] != null) {
                $from = $params["data"]["from"];
                $to = $params["data"]["to"];
                $transactionJoin .= "AND t.conducted_date BETWEEN '$from' AND '$to' ";
            }
        }
        //Logger::logWithMsg("transactionJoin", $transactionJoin);

        $allQuery = "$mainSelect $featuresJoin $characteristicJoin $productJoin $instanceJoin "
            . "$instanceTransactionJoin $transactionJoin $counterpartyJoin";

        //Logger::logWithMsg("allQuery", $allQuery);

        $dbResult = $this->db->query($allQuery)->get();
        return array_values($this->parseDbProducts($dbResult));
    }

    private function getSpecifiedProduct($params, $id) {

    }

    private function parseDbProducts($dbResult) {

        $characteristicsIds = array();
        $instancesIds = array();
        $result = array();
        //Logger::logWithMsg("dbResult", $dbResult);

        foreach ($dbResult as $row) {

            if(!array_key_exists($row->product, $result)) {

                $result[ $row->product ] = array(
                    "id" => $row->product,
                    "title" => $row->title,
                    "category" => $row->category,
                    "article" => $row->article,
                    "barcode" => $row->barcode,
                    "consignment" => $row->consignment,
                    "manufacturer" => $row->manufacturer,
                    "model" => $row->model,
                    "series" => $row->series,
                    "specification" => $row->specification,
                    "total_count" => 0,
                    "characteristics" => array(),
                    "instances" => array()
                );

                $characteristicsIds[ $row->product ] = array();
                $instancesIds[ $row->product ] = array();
            }

            if(!array_key_exists($row->characteristic, $characteristicsIds[$row->product])) {

                array_push( $result[ $row->product ]["characteristics"], array(
                    "id" => $row->characteristic,
                    "name" => $row->name,
                    "type" => $row->type,
                    "measure" => $row->measure,
                    "value" => $row->value
                ));
                $characteristicsIds[$row->product][ $row->characteristic ] = true;
            }

            if(!array_key_exists($row->instance, $instancesIds[$row->product])) {

                array_push( $result[ $row->product ]["instances"], array(
                    "id" => $row->instance,
                    "count" => $row->count,
                    "buying_count" => $row->buying_count,
                    "buying_price" => $row->buying_price,
                    "currency" => $row->currency,
                    "date" => $row->date,
                    "counterparty" => array(
                        "id" => $row->counterparty,
                        "title" => $row->counterparty_title
                    )
                ));
                $instancesIds[$row->product][ $row->instance ] = true;
                $result[ $row->product ]["total_count"] += intval($row->count);
            }
        }
        return $result;
    }
}
