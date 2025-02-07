<?php

require_once __DIR__ . '/../vendor/autoload.php';


use App\core\Router;
use App\controllers\frontOffice\ArticleController;
use App\controllers\Authentication\AuthController;
use App\controllers\frontOffice\HomeController;
use App\controllers\backsOffice\DashboardController;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;



$router = new Router();

$router->get('/', HomeController::class, 'index');
$router->get('/', ArticleController::class, 'showArticles');



$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');

$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');

$router->dispatch();





