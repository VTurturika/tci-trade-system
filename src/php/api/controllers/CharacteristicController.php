<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class CharacteristicController {

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function get(Request $request, Response $response, $args) {

        $response->getBody()->write("get: Not implemented yet");
        return $response;
    }

    public function create(Request $request, Response $response) {

        $response->getBody()->write("create: Not implemented yet");
        return $response;
    }

    public function modify(Request $request, Response $response, $args) {

        $response->getBody()->write("modify: Not implemented yet");
        return $response;
    }

    public function delete(Request $request, Response $response, $args) {

        $response->getBody()->write("delete: Not implemented yet");
        return $response;
    }
}