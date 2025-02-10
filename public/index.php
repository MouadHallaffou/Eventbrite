<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Router;
use App\controllers\frontOffice\EventController;
use App\controllers\Authentication\AuthController;
// use App\controllers\backsOffice\ParticipantController;



$router = new Router();



// $router->get('/participant/add', ParticipantController::class, 'addparticipant');

$router->get('/addEvent', ParticipantController::class, 'affichevent');
$router->post('/addEvent', EventController::class, 'createEvent');

$router->get('/addEvent', EventController::class, 'displayEventForm');
$router->get('/addEvent', EventController::class, 'afficheEvents');

$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');

$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');

$router->dispatch();
