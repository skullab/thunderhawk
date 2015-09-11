<?php

namespace Thunderhawk\API\Mvc;
use Thunderhawk\API\Http\Response;
use Thunderhawk\API\Service;

abstract class Controller extends \Phalcon\Mvc\Controller {
	
	private $moduleInstance = null ;
	
	public function initialize(){
		$ref = new \ReflectionClass($this);
		$namespace = str_replace(basename($ref->getNamespaceName()),'',$ref->getNamespaceName());
		$this->moduleInstance = $this->getDI()->get($namespace.'Module');
		$this->tag->setTitleSeparator(' - ');
		$this->tag->setTitle('thunderhawk');
		$this->onInitialize();
		$this->onPrepareAssets();
		$this->onPrepareThemeAssets();
	}
	
	protected function onPrepareAssets(){
		$this->assets->requireJquery();
		$this->assets->requireAngular();
		$this->assets->requireSemantic();
	}
	
	protected function onPrepareThemeAssets(){
		$src = TH_BASE_DIR .$this->assets->getPathTheme('css/');
		$themeCss = TH_BASE_DIR.'core/ui/themes/'.Service::get(Service::THEME_NAME).'/assets/css' ;
		$compare = fileList($themeCss);
		unlinkDifferent($src, $compare);
		
		$list = fileList($src);
		foreach ($list as $file){
			$this->assets->requireCss($this->assets->getPathTheme('css/'.$file));
		}
		
		$src = TH_BASE_DIR .$this->assets->getPathTheme('js/');
		$themeJs = TH_BASE_DIR.'core/ui/themes/'.Service::get(Service::THEME_NAME).'/assets/js' ;
		$compare = fileList($themeJs);
		unlinkDifferent($src, $compare);
		
		$list = fileList($src);
		foreach ($list as $file){
			$this->assets->requireJs($this->assets->getPathTheme('js/'.$file));
		}
		
		$themeImg = TH_BASE_DIR.'core/ui/themes/'.Service::get(Service::THEME_NAME).'/assets/img' ;
		$compare = fileList($themeImg);
		unlinkDifferent($src, $compare);
	}
	
	protected function onInitialize(){}
	
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