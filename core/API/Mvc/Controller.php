<?php

namespace Thunderhawk\API\Mvc;
use Thunderhawk\API\Http\Response;

abstract class Controller extends \Phalcon\Mvc\Controller {
	
	private $moduleInstance = null ;
	
	public function initialize(){
		$ref = new \ReflectionClass($this);
		$namespace = str_replace(basename($ref->getNamespaceName()),'',$ref->getNamespaceName());
		$this->moduleInstance = $this->getDI()->get($namespace.'Module');
		$this->tag->setTitleSeparator(' - ');
		$this->tag->setTitle('thunderhawk');
		$this->onInitialize();
	}
	
	protected function onInitialize(){
		$this->assets->requireJquery();
		$this->assets->requireAngular();
		$this->assets->requireSemantic();
		
		$src = TH_BASE_DIR . '/' .$this->assets->getPathTheme('css/');
		$list = fileList($src);
		foreach ($list as $file){
			$this->assets->requireCss($this->assets->getPathTheme('css/'.$file));
		}
		
		$src = TH_BASE_DIR . '/' .$this->assets->getPathTheme('js/');
		$list = fileList($src);
		foreach ($list as $file){
			$this->assets->requireJs($this->assets->getPathTheme('js/'.$file));
		}
	}
	
	protected function getModule(){
		return $this->moduleInstance ;
	}
	
	public function indexAction(){}
	
	public function errorAction(){
		$args = func_get_args();
		var_dump($this->moduleInstance->getName());
		echo '<div><center><h1>'.$args[0].'</h1><span>'.$args[1].'</span></center></div>' ;
	}
	
	protected function ajax($payload){
		$status      = 200;
		$description = 'OK';
		$headers     = array();
		$contentType = 'application/json';
		$content     = json_encode($payload);
		
		$response = new Response();
		
		$response->setStatusCode($status, $description);
		$response->setContentType($contentType, 'UTF-8');
		$response->setContent($content);
		
		// Set the additional headers
		foreach ($headers as $key => $value) {
			$response->setHeader($key, $value);
		}
		
		$this->view->disable();
		
		return $response;
	}
}