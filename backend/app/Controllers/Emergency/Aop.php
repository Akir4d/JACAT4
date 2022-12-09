<?php

namespace App\Controllers\Emergency;

class Aop extends BaseController
{

    public function getIndex()
    {
        return $this->aopRender("emergency", "emergency/aop", ['api' => base_url("emergency")]);
    }

}
