<?php
namespace App\controllers\frontOffice;

require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Core\View;
use App\models\User;
use App\core\Session;
class ProfileController extends User
{
    public function index()
    {
        Session::checkSession();
        if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
            die("Error: User is not logged in. Please <a href='/login'>log in</a>.");
        }
        $id = $_SESSION['userId'];
        // $userModel = new User();
        // var_dump($_SESSION);
        $user = $this->findById($id);
        if (!$user) {
            die("Error: User not found.");
        }
        echo View::render('back/Profile/profile.twig', ['user' => $user]);
    }
}