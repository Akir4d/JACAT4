<?php
namespace App\Controllers;

class Home extends BaseController
{

	public function index()
	{
		$this->render('home', 'full_width');
	}
}
