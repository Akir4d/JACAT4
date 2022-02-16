<?php

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller;
/**
 * This base is basically a multipurpose compatibiity layer that extends your imported library
 * Extends your old controller from here
 * more info: https://github.com/kenjis/ci3-to-4-upgrade-helper
 * 
 * @package App\Controllers
 */
class BaseJacatLegacy extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
    }
}
