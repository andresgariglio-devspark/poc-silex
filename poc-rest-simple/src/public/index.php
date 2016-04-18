<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

spl_autoload_register(function ($classname) {
    require ("../classes/" . $classname . ".php");
});


// Config
$config['displayErrorDetails'] = true;

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

// Logs
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// Database
$container['db'] = function ($c) {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    return $client;
};

$app->get('/persons/{id}', function (Request $request, Response $response, $args) {
    $person_id = $args['id'];
    $this->logger->addInfo("Person by id $person_id");
    $collection = $this->db->poc->person;

    $person = $collection->findOne(array('_id' => new MongoDB\BSON\ObjectID($person_id)));

    if ($person === NULL) return $response->withStatus(404);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($person));
});

$app->get('/persons', function (Request $request, Response $response) {
    $this->logger->addInfo("Persons list");

    $collection = $this->db->poc->person;
    $cursor = $collection->find();

    foreach ($cursor as $document) {
        $output['results'][] = $document;
    }

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($output));
});

$app->post('/persons', function (Request $request, Response $response) {
    $this->logger->addInfo("Persons POST");

    $parsedBody = $request->getParsedBody();

    if (!$parsedBody['firstName']) {
      return $response->withStatus(400, 'First name is required.');
    }

    if (!$parsedBody['lastName']) {
      return $response->withStatus(400, 'Last name is required.');
    }

    $collection = $this->db->poc->person;
    $collection->insertOne($parsedBody);

    return $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($parsedBody, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});

$app->delete('/persons/{id}', function (Request $request, Response $response, $args) {
    $person_id = $args['id'];
    $this->logger->addInfo("Delete person by id $person_id");
    $collection = $this->db->poc->person;

    $person = $collection->findOneAndDelete(array('_id' => new MongoDB\BSON\ObjectID($person_id)));

    if ($person === NULL) return $response->withStatus(404);

    return $response->withStatus(204)
        ->withHeader('Content-Type', 'application/json');
});


$app->run();
