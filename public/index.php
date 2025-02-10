<?php

require_once __DIR__ . '/../vendor/autoload.php';


use App\core\Router;
use App\controllers\frontOffice\EventController;
use App\controllers\Authentication\AuthController;
use App\controllers\frontOffice\HomeController;
use App\controllers\backsOffice\AdminController;
use App\core\Session;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;



$router = new Router();
Session::checkSession();

$router->get('/', HomeController::class, 'index');
$router->get('/home', HomeController::class, 'index');

$router->get('/dashboard', AdminController::class, 'index');




$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');

$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');

$router->dispatch();





