<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Session;
use App\core\Router;
use App\controllers\frontOffice\EventController;
use App\controllers\Authentication\AuthController;
use App\controllers\frontOffice\HomeController;
use App\controllers\backsOffice\AdminController;
use App\controllers\backsOffice\CategoryController;
use App\controllers\backsOffice\RoleController;
use App\controllers\backsOffice\UserController;
use App\controllers\frontOffice\ContactController;
use App\Controllers\PaymentController;
use App\core\Validator;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$router = new Router();
Session::checkSession();

$router->get('/', HomeController::class, 'index');
$router->get('/home', HomeController::class, 'index');


$router->get('/home', EventController::class, 'displayEventsAcceptedHome');
$router->get('/', EventController::class, 'displayEventsAcceptedHome');

$router->get('/FindEvents', HomeController::class, 'findevents');
// $router->post('/FindEvents', EventController::class, 'searchEvents');
$router->post('/FindEvents/search', EventController::class, 'searchEvents');
$router->get('/FindEvents', EventController::class, 'displayEvents');
$router->get('/EventDataille/{id}', EventController::class, 'eventDataille');


$router->get('/help/contact', ContactController::class, 'index');
$router->get('/help', ContactController::class, 'helpcenter');

// /------------------ Admin

$router->get('/dashboard', AdminController::class, 'index');
$router->get('/dashboard/Event', AdminController::class, 'showStatusEvents');
$router->post('/dashboard/Event/updateStatus', AdminController::class, 'updateEventsStatus');



// /------------------ categories

$router->get('/dashboard/categories', CategoryController::class, 'index');
$router->post('/dashboard/categories/delete', CategoryController::class, 'deleteCategories');
$router->post('/dashboard/categories/add', CategoryController::class, 'addCategories');
$router->get('/dashboard/categories/update/{id}', CategoryController::class, 'editCategories');
$router->post('/dashboard/categories/update', CategoryController::class, 'updateCategories');

// // /------------------Management role

$router->get('/dashboard/role', RoleController::class, 'index');
$router->post('/dashboard/role/delete', RoleController::class, 'deleteRole');
$router->post('/dashboard/role', RoleController::class, 'addRoles');
$router->get('/dashboard/role/update/{id}', RoleController::class, 'editRole');
$router->post('/dashboard/role/update', RoleController::class, 'updateRoles');


$router->post('/addEvent', EventController::class, 'createEvent');
$router->get('/addEvent', EventController::class, 'displayEventForm');
$router->get('/addEvent', EventController::class, 'afficheEvents');
$router->get('/addEvent', EventController::class, 'afficherTousLesEvenements');
$router->get('/events', EventController::class, 'afficherTousLesEvenements');
$router->get('/statistics', EventController::class, 'statisticsOrganisateur');
$router->post('/searchEvents', EventController::class, 'searchEvents');


$router->get('/get-villes-by-region', EventController::class, 'getVillesByRegion');

// Route pour créer un événement
$router->post('/create-event', EventController::class, 'createEvent');
$router->post('/delete-event', EventController::class, 'deleteEvent');
$router->get('/edit-event/{event_id}', EventController::class, 'editEvent');
$router->post('/update-event/{event_id}', EventController::class, 'updateEvent');


$router->post('/dashboard/user/delete', UserController::class, 'deleteUser'); // Add this route
$router->post('/dashboard/user/userStatus', UserController::class, 'updateStatus');
$router->get('/dashboard/users', UserController::class, 'index');

// /------------------ categories
$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');
$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');

$router->dispatch();