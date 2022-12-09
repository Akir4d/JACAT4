<?php

namespace App\Controllers;

class Aop extends AopBaseController
{

    public function getIndex()
    {
        return $this->aopRender("main", "/", ['api' => base_url("emergency/aop")]);
    }

}