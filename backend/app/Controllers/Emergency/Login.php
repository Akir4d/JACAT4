<?php

namespace App\Controllers\Emergency;

use App\Controllers\Emergency\BaseController;
use \CodeIgniter\HTTP\ResponseInterface;

class Login extends BaseController
{

    public function postLogin()
    {
 
        $log = new \Config\Emergency();
        if ($json = $this->request->getJSON()) {
            $keys = array_keys((array) $json);
            foreach ($keys as $key) {
                switch ($key) {
                    case 'username':
                        if (password_verify($json->username, $log->login['username']) && password_verify($json->password, $log->login['password'])) {
                            return $this->renderJson([
                                'id' => 1,
                                'username' => $json->username,
                                'password' => '********',
                                'firstName' => $log->login['firstname'],
                                'lastName' => $log->login['lastname'],
                                'other' => $log->login['username'],
                                'token' => $this->jwtEncode(['username' => $json->username])]);
                        }
                        break;
                }
            }
            return $this->renderJson(['status' => 'forbidden'], ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function postChecks(){
        $ret[] = $this->checkDb();
        $ret[] = $this->checkCredentials();
        return $this->renderJson($ret);
    }

}
