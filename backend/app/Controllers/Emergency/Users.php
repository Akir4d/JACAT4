<?php

namespace App\Controllers\Emergency;

class Users extends Auth
{
    public function index()
    {
        //
    } 

    public function postAdminUser(){
        if($json = $this->request->getJSON()){
            $env = file_get_contents(APPPATH . '../.env');
            $env = $this->replaceOnEnv('emergency.login.firstname', $json->firstName, $env);
            $env = $this->replaceOnEnv('emergency.login.lastname', $json->lastName, $env);
            $env = $this->replaceOnEnv('emergency.login.username', password_hash($json->username, PASSWORD_DEFAULT), $env);
            $env = $this->replaceOnEnv('emergency.login.password', password_hash($json->password, PASSWORD_DEFAULT), $env);
            file_put_contents(APPPATH . '../.env', $env);
        }
        $json->id = 1;
        return $this->renderJson((array)$json);
    }
}
