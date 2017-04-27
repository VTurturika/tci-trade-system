<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class TransactionController {

    protected $container;
    private $model;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->model = new TransactionModel();
    }

    public function get(Request $request, Response $response) {

        $response->getBody()->write("get: Not implemented yet");
        return $response;
    }

    public function create(Request $request, Response $response) {

        $response->getBody()->write("create: Not implemented yet");
        return $response;
    }
}