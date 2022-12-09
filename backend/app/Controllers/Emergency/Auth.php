<?php

namespace App\Controllers\Emergency;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class Auth extends BaseController
{

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = \Config\Services::session();
        $this->authBearer();

    }

    private function authBearer()
    {
        $inp = $this->request->getServer("HTTP_AUTHORIZATION");
        $spr = ($inp == null) ? ' ' : $inp;
        $test = explode(' ', $spr);
        $error = false;
        if ($test[0] == "Bearer") {
            if (array_key_exists(1, $test) && $this->jwtDecode($test[1])->error)
                $error = "Token Invalid";
        } else {
            $error = "no credentials";
        }
        if($error !== false) {
            header("Content-Type: application/json");
            http_response_code(ResponseInterface::HTTP_UNAUTHORIZED);
            echo json_encode(['status' => $error, 'debug'=>$inp], );
            die();
        }
    }

}
