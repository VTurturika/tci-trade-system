<?php

class Controller {

    private $app;

    public function __construct() {

        $this->app = new Slim\App;

        $container = $this->app->getContainer();

        $container['CategoryController'] = function($c) { return new CategoryController($c); };
        $container['CharacteristicController'] = function($c) { return new CharacteristicController($c); };
        $container['CounterpartyController'] = function($c) { return new CounterpartyController($c); };
        $container['ProductController'] = function($c) { return new ProductController($c); };
        $container['TransactionController'] = function($c) { return new TransactionController($c); };
    }

    public function route() {

        //category routes
        $this->app->get("/api/category/get[/{id}]", \CategoryController::class . ":get");
        $this->app->post("/api/category/create", \CategoryController::class . ":create");
        $this->app->post("/api/category/modify/{id}", \CategoryController::class . ":modify");
        $this->app->post("/api/category/delete/{id}", \CategoryController::class . ":delete");

        //characteristic routes
        $this->app->get("/api/characteristic/get[/{id}]", \CharacteristicController::class . ":get");
        $this->app->post("/api/characteristic/create", \CharacteristicController::class . ":create");
        $this->app->post("/api/characteristic/modify/{id}", \CharacteristicController::class . ":modify");
        $this->app->post("/api/characteristic/delete/{id}", \CharacteristicController::class . ":delete");

        //counterparty routes
        $this->app->get("/api/counterparty/get[/{id}]", \CounterpartyController::class . ":get");
        $this->app->post("/api/counterparty/create", \CounterpartyController::class . ":create");
        $this->app->post("/api/counterparty/modify/{id}", \CounterpartyController::class . ":modify");
        $this->app->post("/api/counterparty/delete/{id}", \CounterpartyController::class . ":delete");

        //product routes
        $this->app->post("/api/product/get[/{id}]", \ProductController::class . ":get");
        $this->app->post("/api/product/create", \ProductController::class . ":create");
        $this->app->post("/api/product/modify/{id}", \ProductController::class . ":modify");

        //transaction routes
        $this->app->post("/api/transaction/get", \TransactionController::class . ":get");
        $this->app->post("/api/transaction/create", \TransactionController::class . ":create");
        $this->app->post("/api/transaction/conduct/{id}", \TransactionController::class . ":conduct");

        $this->app->run();
    }
}
