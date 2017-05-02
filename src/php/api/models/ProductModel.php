<?php

class ProductModel extends Model {

    private $select =
        "SELECT pc.product, p.title, p.category, p.article, p.barcode, p.consignment, p.manufacturer, "
        . "p.model, p.series, p.specification, pc.characteristic, c.name, c.type, c.measure, pc.value, "
        . "it.instance, i.count, i.price, i.currency, "
        . "t.conducted_date AS date, it.counterparty, ct.title AS counterparty_title "
        . "FROM Product_Characteristic pc";

    private $characteristicJoin = "JOIN Characteristic c ON c.id = pc.characteristic";
    private $counterpartyJoin =  "JOIN Counterparty ct ON ct.id = it.counterparty ";
    private $featuresJoin = "";
    private $productJoin = "JOIN Product p ON pc.product = p.id ";
    private $instanceJoin = "JOIN Instance i ON p.id = i.product ";
    private $instanceTransactionJoin = "JOIN Instance_Transaction it ON i.id = it.instance AND it.type = 0 ";
    private $transactionJoin = "JOIN Transaction t ON t.id = it.transaction ";

    public function get($params, $id) {

        if($params == null) $params = array();
        Logger::logWithMsg("params", $params);

        return ($id == null) ? $this->getFilteredProducts($params) : $this->getSpecifiedProduct($id);
    }

    public function create($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("params", $params);

        if (array_key_exists("title", $params) &&
            array_key_exists("category", $params) &&
            array_key_exists("article", $params)
        ) {

            $data = array();
            $data["title"] = $params["title"];
            $data["category"] = $params["category"];
            $data["article"] = $params["article"];

            if(array_key_exists("description", $params))
                $data["description"] = $params["description"];
            if(array_key_exists("barcode", $params))
                $data["barcode"] = $params["barcode"];
            if(array_key_exists("consignment", $params))
                $data["consignment"] = $params["consignment"];
            if(array_key_exists("manufacturer", $params))
                $data["manufacturer"] = $params["manufacturer"];
            if(array_key_exists("model", $params))
                $data["model"] = $params["model"];
            if(array_key_exists("series", $params))
                $data["series"] = $params["series"];
            if(array_key_exists("specification", $params))
                $data["specification"] = $params["specification"];
            if(array_key_exists("comment", $params))
                $data["comment"] = $params["comment"];

            //todo check if category exist
            //todo for each characteristic check if it exist

            $id = $this->db->table("Product")->insert($data);
            $data["id"] = $id;

            if(array_key_exists("characteristics", $params) && count($params["characteristics"]) > 0) {

                $data["characteristics"] = array();
                foreach ($params["characteristics"] as $c) {

                    $this->db->table("Product_Characteristic")->insert(array(
                        "product" => $id,
                        "characteristic" => $c["id"],
                        "value" => $c["value"]
                    ));

                    array_push($data["characteristics"], array(
                        "characteristic" => $c["id"],
                        "value" => $c["value"],
                        "type" => $c["type"],
                        "measure" => $c["measure"]
                    ));
                }
            }
            return $data;
        }
        else return array("msg" => "error: title, category, article required");
    }

    public function modify($params, $id) {

        if($params == null) $params = array();

        $data = array();

        if(array_key_exists("title", $params))
            $data["title"] = $params["title"];
        if(array_key_exists("category", $params))
            $data["category"] = $params["category"];
        if(array_key_exists("article", $params))
            $data["article"] = $params["article"];
        if(array_key_exists("description", $params))
            $data["description"] = $params["description"];
        if(array_key_exists("barcode", $params))
            $data["barcode"] = $params["barcode"];
        if(array_key_exists("consignment", $params))
            $data["consignment"] = $params["consignment"];
        if(array_key_exists("manufacturer", $params))
            $data["manufacturer"] = $params["manufacturer"];
        if(array_key_exists("model", $params))
            $data["model"] = $params["model"];
        if(array_key_exists("series", $params))
            $data["series"] = $params["series"];
        if(array_key_exists("specification", $params))
            $data["specification"] = $params["specification"];
        if(array_key_exists("comment", $params))
            $data["comment"] = $params["comment"];

        $this->db->table("Product")->where("id", "=", $id)->update($data);
        $this->db->table("Product_Characteristic")->where("product", "=", $id)->delete();
        $data["id"] = $id;

        if(array_key_exists("characteristics", $params) && count($params["characteristics"]) > 0) {

            $data["characteristics"] = array();
            foreach ($params["characteristics"] as $c) {

                $this->db->table("Product_Characteristic")->insert(array(
                    "product" => $id,
                    "characteristic" => $c["id"],
                    "value" => $c["value"]
                ));

                array_push($data["characteristics"], array(
                    "characteristic" => $c["id"],
                    "value" => $c["value"],
                    "type" => $c["type"],
                    "measure" => $c["measure"]
                ));
            }
        }
        return $data;
    }

    private function getFilteredProducts($params) {

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
                $this->featuresJoin .= $featureSelect;
                $i++;
            }

            if (strpos($this->featuresJoin, ") f1 ") !== false) {
                $this->featuresJoin .= ") products ON pc.product = products.ids";
            }
            else $this->featuresJoin = "";
        }
        //Logger::logWithMsg("featuresJoin", $this->featuresJoin);

        if(array_key_exists("title", $params)) {
            $title = $params["title"];
            $this->productJoin .= "AND p.title = '$title' ";
        }
        if(array_key_exists("category", $params)) {
            $category = $params["category"];
            $this->productJoin .= "AND p.category = $category ";
        }
        if(array_key_exists("article", $params)) {
            $article = $params["article"];
            $this->productJoin .= "AND p.article = '$article' ";
        }
        if(array_key_exists("barcode", $params)) {
            $barcode = $params["barcode"];
            $this->productJoin .= "AND p.barcode = '$barcode' ";
        }
        if(array_key_exists("consignment", $params)) {
            $consignment = $params["consignment"];
            $this->productJoin .=  "AND p.consignment = '$consignment' ";
        }
        if(array_key_exists("manufacturer", $params)) {
            $manufacturer = $params["manufacturer"];
            $this->productJoin .= "AND p.manufacturer = '$manufacturer' ";
        }
        if(array_key_exists("model", $params)) {
            $model = $params["model"];
            $this->productJoin .= "AND p.model = '$model' ";
        }
        if(array_key_exists("series", $params)) {
            $series = $params["series"];
            $this->productJoin .= "AND p.series = '$series' ";
        }
        if(array_key_exists("specification", $params)) {
            $specification = $params["specification"];
            $this->productJoin .= "AND p.specification = '$specification' ";
        }
        //Logger::logWithMsg("productJoin", $this->productJoin);

        if(array_key_exists("price", $params)) {

            if($params["price"]["from"] == null && $params["price"]["to"] != null) { //buying_price <= to
               $this->instanceJoin .= "AND i.price <= " . floatval($params["price"]["to"]) . " ";
            }
            else if ($params["price"]["from"] != null && $params["price"]["to"] == null) { //buying_price >= from
                $this->instanceJoin .= "AND i.price >= " . floatval($params["price"]["from"]) . " ";
            }
            else if ($params["price"]["from"] != null && $params["price"]["to"] != null) {
                $this->instanceJoin .= "AND i.price BETWEEN "
                    . floatval($params["price"]["from"]) . " AND "
                    . floatval($params["price"]["to"]) . " ";
            }
        }
        if(array_key_exists("count", $params)) {


            if($params["count"]["from"] == null && $params["count"]["to"] != null) { //current_count <= to
                $this->instanceJoin .= "AND i.count <= " . floatval($params["count"]["to"]) . " ";
            }
            else if ($params["count"]["from"] != null && $params["count"]["to"] == null) { //buying_price >= from
                $this->instanceJoin .= "AND i.count >= " . floatval($params["count"]["from"]) . " ";
            }
            else if ($params["count"]["from"] != null && $params["count"]["to"] != null) {
                $this->instanceJoin .= "AND i.count BETWEEN " . floatval($params["count"]["from"])
                    . " AND " . floatval($params["count"]["to"]) . " ";
            }
        }
        if(array_key_exists("storage", $params)) {
            $storage = $params["storage"];
            $this->instanceJoin .= "AND i.storage = '$storage' ";
        }
        if(array_key_exists("currency", $params)) {
            $currency = $params["currency"];
            $this->instanceJoin .= "AND i.currency = '$currency' ";
        }
        //Logger::logWithMsg("instanceJoin", $this->instanceJoin);

        if(array_key_exists("counterparty", $params)) {
            $counterparty = $params["counterparty"];
            $this->instanceTransactionJoin .= "AND it.counterparty = $counterparty ";
        }
        //Logger::logWithMsg("instanceTransactionJoin", $this->instanceTransactionJoin);

        if(array_key_exists("data", $params)) {

            if($params["data"]["from"] == null && $params["data"]["to"] != null) { //data <= to
                $to = $params["data"]["to"];
                $this->transactionJoin .= "AND t.conducted_date <= '$to' ";
            }
            else if ($params["data"]["from"] != null && $params["data"]["to"] == null) { //data >= from
                $from = $params["data"]["from"];
                $this->transactionJoin .= "AND t.conducted_date >= '$from' ";
            }
            else if ($params["data"]["from"] != null && $params["data"]["to"] != null) {
                $from = $params["data"]["from"];
                $to = $params["data"]["to"];
                $this->transactionJoin .= "AND t.conducted_date BETWEEN '$from' AND '$to' ";
            }
        }
        //Logger::logWithMsg("transactionJoin", $this->transactionJoin);

        $fullQuery = "$this->select $this->featuresJoin "
            . "$this->characteristicJoin $this->productJoin $this->instanceJoin "
            . "$this->instanceTransactionJoin $this->transactionJoin $this->counterpartyJoin";

        //Logger::logWithMsg("$fullQuery", $fullQuery);

        $dbResult = $this->db->query($fullQuery)->get();
        return array_values($this->parseDbProducts($dbResult));
    }

    private function getSpecifiedProduct($id) {


        $fullQuery = "$this->select $this->productJoin AND p.id = $id "
            . "$this->characteristicJoin $this->instanceJoin "
            . "$this->instanceTransactionJoin $this->transactionJoin $this->counterpartyJoin";

        $dbResult = $this->db->query($fullQuery)->get();
        return array_values($this->parseDbProducts($dbResult))[0];
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
                    "price" => $row->price,
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
