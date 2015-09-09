<?php

namespace Thunderhawk\Modules\Test\Controllers;

use Thunderhawk\API\Engine;
use Thunderhawk\API\Service;
use Thunderhawk\API\Mvc\Controller;
use Thunderhawk\API\Debug\Log;
use Thunderhawk\API\Tokenizer;
use Thunderhawk\API\Autoloader;

use Thunderhawk\Modules\Test\Models\Users;
use Thunderhawk\API\Mvc\Model\Resultset;

class IndexController extends Controller {
	
	const USER_CREATE = 'create' ;
	const USER_DELETE = 'delete' ;
	const USER_UPDATE = 'update' ;
	const USER_LOAD   = 'load' ;
			
	public function indexAction() {
		
		//$this->assets->requireJqueryui();
		$this->assets->requireJquery();
		
		//$this->assets->requireCustomCss('jtable/2.4.0/themes/metro/blue/jtable.min.css',true,false);
		//$this->assets->requireCustomJs('jtable/2.4.0/jquery.jtable.js',true,false);
		//$this->assets->requireCustomJs('jtable/2.4.0/localization/jquery.jtable.it.js',true,false);
		//$this->assets->requireModuleJs('js/test.js');
		
		$this->assets->requireSemantic();
		$this->assets->requireAngular();
		//$this->assets->requireOps();
		
	}
		
	public function usersAction(){
		
		$method = $this->dispatcher->getParam('method');
		
		switch ($method){
			case self::USER_LOAD:
				return $this->loadUsers();
			case self::USER_CREATE:
				return $this->createUser();
			case self::USER_DELETE:
				return $this->deleteUser();
			case self::USER_UPDATE:
				return $this->updateUser();
		}
	}
	
	private function loadUsers(){
		$method = $this->dispatcher->getParam('method');
		var_dump($method);
		$filter = @$_POST['name'];
		$users = Users::find(array(
				"name LIKE '%$filter%'",
				'order'	=> $_GET['jtSorting'],
				'limit'	=> array('number' => $_GET['jtPageSize'] , 'offset' => $_GET['jtStartIndex'])
				
		));
		$table = $users->toArray();
		$count = Users::count();
		$payload = array('Result'=>'OK','Records'=>$table,'TotalRecordCount'=>$count);
		
		return $this->ajax($payload);
	}
	
	private function createUser(){
		$user = new Users();
		$user->create($_POST);
		$last = Users::find();
		$payload = array('Result' => 'OK','Record'=>$last->getLast()->toArray());

		return $this->ajax($payload);
	}
	
	private function deleteUser(){
		$user = Users::find(array('id = '.@$_POST['id']));
		if(count($user) == 1){
			$user->delete();
		}
		$payload = array('Result'=>'OK');
		return $this->ajax($payload);
	}
	
	private function updateUser(){
		$user = new Users();
		$user->save($_POST);
		$payload = array('Result'=>'OK');
		return $this->ajax($payload);
	}
}