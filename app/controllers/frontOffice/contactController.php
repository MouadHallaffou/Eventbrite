<?php

namespace App\controllers\frontOffice;

use App\core\Controller;


class ContactController extends Controller
{
    public function index()
    {
        $this->render('pages/Contact.twig');
    }
    public function helpcenter()
    {
        $this->render('pages/help.twig');
    }
}
