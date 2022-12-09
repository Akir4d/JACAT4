<?php

namespace App\Controllers\Emergency;


class Options extends BaseController
{
    public function index()
    {
        return $this->optionsHandler();
    }

    public function optionsHandler(){
        header("Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Headers, Authorization, observe, enctype, Content-Length, X-Csrf-Token");
        header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, PATCH, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        header("HTTP/1.1 200 OK");
        return die();
    }
}
