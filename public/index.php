<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Router;
use App\controllers\frontOffice\EventController;
use App\controllers\Authentication\AuthController;
use App\controllers\frontOffice\HomeController;
use App\controllers\backsOffice\AdminController;
use App\controllers\frontOffice\ContactController;
use App\controllers\frontOffice\ProfileController;


use App\core\Session;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new Router();
Session::checkSession();

$router->get('/', HomeController::class, 'index');
$router->get('/home', HomeController::class, 'index');
$router->get('/help/contact', ContactController::class, 'index');
$router->get('/help', ContactController::class, 'helpcenter');

$router->get('/profile', ProfileController::class, 'index');



// $router->get('/', EventController::class, 'displayEventsAcceptedHome');



$router->get('/dashboard', AdminController::class, 'index');

$router->post('/addEvent', EventController::class, 'createEvent');
$router->get('/addEvent', EventController::class, 'displayEventForm');
$router->get('/addEvent', EventController::class, 'afficherTousLesEvenements');
$router->get('/events', EventController::class, 'afficherTousLesEvenements');

// Route pour créer un événement
$router->post('/create-event', EventController::class, 'createEvent');
// Route pour supprimer un événement
$router->post('/delete-event', EventController::class, 'deleteEvent');
// Route pour modifier un événement
$router->get('/edit-event/{event_id}', EventController::class, 'editEvent');
$router->post('/update-event/{event_id}', EventController::class, 'updateEvent');

$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');

$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');

$router->dispatch();
