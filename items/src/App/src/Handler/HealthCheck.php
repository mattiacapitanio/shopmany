<?php

namespace App\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use \PDO;

class HealthCheck implements RequestHandlerInterface
{
    function __construct() {
        // found error passing params in construct()
        $this->username = 'hello';
        $this->password = 'ciao';
        $this->hostname = "itemdb"; 
        $this->dbname = 'shopmany';
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $statusCode = 500;
        $body = new \stdClass();
        $body->status = "unhealthy";
        $mySqlCheck = new \stdClass();
        $mySqlCheck->name = "mysql";
        $mySqlCheck->status = "unhealthy";
        $mySqlCheck->error = "";
        // $mySqlCheck->conn = "mysql:host=$this->hostname;port=3306;dbname=$this->dbname";

        try {
            $this->pdo = new PDO("mysql:host=$this->hostname;port=3306;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $statusCode = 200;
            $body->status = "healthy";
            $mySqlCheck->status = "healthy";

        } catch(\PDOException $ex){
            $mySqlCheck->error = $ex->getMessage();
        }
        $body->checks = [$mySqlCheck];

        $response = new JsonResponse($body);
        $response = $response->withStatus($statusCode);

        return $response;
    }
}
