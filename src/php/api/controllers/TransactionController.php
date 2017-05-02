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

    public function buy(Request $request, Response $response) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->buy($parsedBody);
        return $response->withJson($responseResult);
    }

    public function sell(Request $request, Response $response) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->sell($parsedBody);
        return $response->withJson($responseResult);
    }

    public function conduct(Request $request, Response $response, $args) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->conduct($parsedBody, $args);
        return $response->withJson($responseResult);
    }
}