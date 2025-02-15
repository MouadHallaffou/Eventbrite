<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\core\Session;
use App\core\Router;
use App\controllers\frontOffice\EventController;
use App\controllers\Authentication\AuthController;
use App\controllers\frontOffice\HomeController;
use App\controllers\backsOffice\AdminController;


use Twig\Environment;
use App\core\Controller;
use Twig\Loader\FilesystemLoader;






$router = new Router();
Session::checkSession();

// /------------------ home 

$router->get('/', HomeController::class, 'index');
$router->get('/home', HomeController::class, 'index');
$router->get('/FindEvents', HomeController::class, 'findevents');

$router->post('/FindEvents', EventController::class, 'searchEvents');
$router->get('/FindEvents', EventController::class, 'searchEvents');



// $router->get('/help/contact', ContactController::class, 'index');
// $router->get('/help', ContactController::class, 'helpcenter');


// /------------------ Admin


$router->get('/home', EventController::class, 'displayEventsAcceptedHome');
$router->get('/', EventController::class, 'displayEventsAcceptedHome');


$router->get('/dashboard', AdminController::class, 'index');
$router->get('/dashboard/user/delete', AdminController::class, 'deleteUser');
$router->post('/dashboard/user/delete', AdminController::class, 'deleteUser'); // Add this route
$router->post('/dashboard/user/userStatus', AdminController::class, 'updateStatus');

// // /------------------ categories
// $router->get('/dashboard/categories', CategoryController::class, 'index');
// $router->post('/dashboard/categories/delete', CategoryController::class, 'deleteCategories');
// $router->post('/dashboard/categories/add', CategoryController::class, 'addCategories');
// $router->get('/dashboard/categories/update/{id}', CategoryController::class, 'editCategories');

// // /------------------Management role

// $router->get('/dashboard/role', RoleController::class, 'index');
// $router->post('/dashboard/role/delete', RoleController::class, 'deleteRole');
// $router->post('/dashboard/role', RoleController::class, 'addRoles');
// // $router->get('/dashboard/roles/update/{id}', roleController::class, 'editRoles');



$router->get('/dashboard/users', AdminController::class, 'updateStatus');
$router->post('/dashboard/user/userStatus', AdminController::class, 'updateStatus');


// /------------------événement

$router->post('/create-event', EventController::class, 'createEvent');
$router->post('/delete-event', EventController::class, 'deleteEvent');
$router->get('/edit-event/{event_id}', EventController::class, 'editEvent');
$router->post('/update-event/{event_id}', EventController::class, 'updateEvent');

// /------------------ categories
$router->get('/register', AuthController::class, 'registerView');
$router->get('/login', AuthController::class, 'loginView');
$router->post('/register', AuthController::class, 'register');
$router->post('/login', AuthController::class, 'login');



$router->dispatch();
