<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class ProductController {

    protected $container;
    private $model;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->model = new ProductModel();
    }

    public function get(Request $request, Response $response, $args) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->get($parsedBody, $args["id"] ?? null);
        return $response->withJson($responseResult);
    }

    public function create(Request $request, Response $response) {

        $response->getBody()->write("create: Not implemented yet");
        return $response;
    }
}