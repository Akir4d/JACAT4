<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Test extends BaseController
{
    public function getIndex($arg="")
    {
        echo '<pre>', var_export($_SERVER), '</pre>';
    }

    public function getLogin(){
        $login = new \CodeIgniter\Shield\Controllers\LoginController;
        return $login->loginView(); 
        //echo '<pre>', var_export($_SERVER), '</pre>';
    }
}
