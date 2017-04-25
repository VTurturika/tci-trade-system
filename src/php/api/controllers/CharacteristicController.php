<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class CharacteristicController {

    protected $container;
    private $model;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->model = new CharacteristicModel();
    }

    public function get(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->get($parsedBody, $args["id"] ?? null);
        Logger::logWithMsg("res ", $responseResult);
        return $response->withJson($responseResult);
    }

    public function create(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->create($parsedBody, $args);
        Logger::logWithMsg("res ", $responseResult);
        return $response->withJson($responseResult);
    }

    public function modify(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->modify($parsedBody, $args);
        Logger::logWithMsg("res ", $responseResult);
        return $response->withJson($responseResult);
    }

    public function delete(Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->delete($parsedBody, $args["id"] ?? null);
        Logger::logWithMsg("res ", $responseResult);
        return $response->withJson($responseResult);
    }
}