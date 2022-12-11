<?php

namespace App\Controllers\Admin;

class Login extends ModuleController
{
    public function getIndex()
    {
        $ex = new \CodeIgniter\Shield\Controllers\LoginController();
       return $ex->loginView();
    }

    public function getMagicLink(){
        $ex = new \CodeIgniter\Shield\Controllers\MagicLinkController();
        return $ex->loginView();
    }

    public function getVerifyMagicLink(){
        $ex = new \CodeIgniter\Shield\Controllers\MagicLinkController();
        return $ex->verify();
    }

    public function getLogout(){
        $ex = new \CodeIgniter\Shield\Controllers\LoginController();
        return $ex->logoutAction();
    }

}
