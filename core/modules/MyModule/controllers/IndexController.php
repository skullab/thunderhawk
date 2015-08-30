<?php

namespace Vendor\App\Controllers;

use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Service;

class IndexController extends Controller {

	public function indexAction(){
		$this->assets->addCss('css/style.css');
	}
}