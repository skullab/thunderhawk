<?php

namespace Thunderhawk\Modules\Installer\Controllers;
use Thunderhawk\API\Mvc\Controller;

class IndexController extends Controller {
	
	public function indexAction(){
		echo 'install' ;
	}
	
	public function successAction(){
		$this->flash->success ( "Installation success !" );
	}
	
	public function installAction(){
		echo 'ok installing...';
	}
}