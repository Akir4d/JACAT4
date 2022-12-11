<?php

namespace App\Controllers;

class Home extends JacatController
{

    public function getIndex()
    {
        return $this->render('home', 'full_width');
    }


}