<?php

class Model  {

    protected $db;

    public function __construct() {

        $this->initDatabaseConnection();
    }

    private function initDatabaseConnection() {

        $config = array(
            "driver"    => "mysql",
            "host"      => "localhost",
            "database"  => "trade_system",
            "username"  => "tci",
            "password"  => "GrQHkGLqSGmVKxMk",
            "charset"   => "utf8"
        );

        $connection = new \Pixie\Connection("mysql", $config);
        $this->db = new \Pixie\QueryBuilder\QueryBuilderHandler($connection);
    }
}