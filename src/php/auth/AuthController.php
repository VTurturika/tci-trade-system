<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

class AuthController {

    protected $container;
    private $model;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->model = new AuthModel();
    }

    public function login(Request $request, Response $response) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->login($parsedBody);
        return $response->withJson($responseResult);
    }

    public function password(Request $request, Response $response) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->changePassword($parsedBody);
        return $response->withJson($responseResult);
    }

    public function refresh(Request $request, Response $response) {

        $parsedBody = $request->getParsedBody();
        $responseResult = $this->model->refreshToken($parsedBody);
        return $response->withJson($responseResult);
    }

}