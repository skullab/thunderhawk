<?php

namespace Thunderhawk\Modules\Frontend\Controllers;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Template\Hook;

class IndexController extends Controller{
	
	public function indexAction(){
		Hook::inflate('header','ciao');
		Hook::inflate('header','mondo');
	}
}