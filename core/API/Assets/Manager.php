<?php
namespace Thunderhawk\API\Assets;

use Thunderhawk\API\Autoloader;
use Thunderhawk\API\Engine;
use Thunderhawk\API\Service;
class Manager extends \Phalcon\Assets\Manager{
	
	private $siteBaseUri = ''; // like url base uri
	private $moduleUri = '' ; // module uri
	
	private $assetsUri = '';
	private $themesUri	= '';
	private $libUri	= '';
	
	private $stackCss = array();
	private $stackJs = array();
 	private $stackCustomJs = array();
 	private $stackCustomCss = array();
 	
 	private $loadedLibraries = array();
 	
 	private $customLibraries = array(
 			'jquery'	=> array(
 					'collection'	=> 'jquery', 
 					'resources'		=> array(
 							'js'	=> array(
 									'path' 		=> 'jquery/jquery.js',
 									'local'		=> true,
 									'filter'	=> true,
 									'attributes'=> null
 							)
 					),
 					
 			),
 			'jqueryui'	=> array(
 					'collection'	=> 'jqueryui',
 					'resources'		=> array(
 							'js'	=> array(
 									'path'		=> 'jqueryui/default/jquery-ui.min.js',
 									'local'		=> true,
 									'filter'	=> false,
 									'attributes'=> null
 							),
 							'css'	=> array(
 									'path'		=> 'jqueryui/default/jquery-ui.min.css',
 									'local'		=> true,
 									'filter'	=> false,
 									'attributes'=> null
 							)
 					),
 					
 			),
 			'angular'	=> array(
 					'collection'	=> 'angular',
 					'resources'		=> array(
 							'js'	=> array(
 									'path' 		=> 'angular/angular.min.js',
 									'local'		=> true,
 									'filter'	=> false,
 									'attributes'=> null
 							)
 					)
 			),
 			'semantic'	=> array(
 					'collection'	=> 'semantic',
 					'resources'		=> array(
 							'js'	=> array(
 									'path' 		=> 'semantic/dist/semantic.min.js',
 									'local'		=> true,
 									'filter'	=> false,
 									'attributes'=> null
 							),
 							'css'	=> array(
 									'path' 		=> 'semantic/dist/semantic.min.css',
 									'local'		=> true,
 									'filter'	=> false,
 									'attributes'=> null
 							)
 					)
 			)
 	);
 	
 	private $customMethods = array();
 	
 	private $customRequireFunction , $customOutputFunction ;
 	
	public function __construct($options = null){
		parent::__construct($options);
		if(array_key_exists('moduleUri', $this->getOptions())){
			$this->moduleUri = $this->getOptions()['moduleUri'];
		}
		
		$this->dirs = Engine::getInstance()->getConfigDirs();
		
		$this->customRequireFunction = function($name){
			if(!array_key_exists($name,$this->loadedLibraries)){
				$this->loadedLibraries[$name] = $this->customLibraries[$name] ;
				$collectionBase = $this->loadedLibraries[$name]['collection'] ;
				foreach ($this->loadedLibraries[$name]['resources'] as $type => $resource){
					$calledFunction = 'add'.ucfirst($type);
					$path = $this->dirs['assets']->lib . $resource['path'] ;
					$this->collection($collectionBase.'-'.$type)->$calledFunction($path,$resource['local'],$resource['filter'],$resource['attributes']);
				}
			}
		};
		
		$this->customOutputFunction = function($name){
			if(!array_key_exists($name, $this->loadedLibraries))return;
			foreach ($this->loadedLibraries[$name]['resources'] as $type => $resource){
				$collection = $this->loadedLibraries[$name]['collection'] . '-' . $type ;
				$calledFunction = 'output'.ucfirst($type) ;
				$this->$calledFunction($collection);
			}
		};
		
		$this->initializeCustomMethods();
		
		$this->siteBaseUri = Service::get(Service::URL)->getBaseUri();
		
		$this->assetsUri = $this->dirs['public']->assets ;
		$this->standardUri = $this->dirs['assets']->standard ;
		$this->themesUri = $this->dirs['assets']->themes ;
		$this->libUri = $this->dirs['assets']->lib ;
		
		$this->collection('customjs');
		$this->collection('customcss');
	}
	
	private function initializeCustomMethods(){
		foreach ($this->customLibraries as $name => $library){
			$this->customMethods['require'.ucfirst($name)] = \Closure::bind($this->customRequireFunction, $this,get_class());
			$this->customMethods['output'.ucfirst($name)] = \Closure::bind($this->customOutputFunction,$this,get_class());
		}
	}
	function __call($method,$args){
		if(@is_callable($this->customMethods[$method])){
			$name = strpos($method,'require') !== false ? strtolower(str_replace('require', '', $method)) : strtolower(str_replace('output', '', $method)) ;
			return call_user_func_array($this->customMethods[$method], array($name));
		}else{
			var_dump('no callable method '.$method);
		}
	}
	/*************************************************************************/
	// PUBLIC JS/CSS
	/*************************************************************************/
	
	public function addCss ($path, $local = true, $filter = true, $attributes = null){
		$this->stackCss[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=> $attributes
		);
		parent::addCss($path,$local,$filter,$attributes);
	}
	
	public function addJs ($path, $local = true, $filter = true, $attributes = null){
		$this->stackJs[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=>$attributes
		);
		parent::addJs($path,$local,$filter,$attributes);
	}
	
	public function requireCss($path, $local = true, $filter = true, $attributes = null){
		if(!array_key_exists($path, $this->stackCss)){
			$this->stackCss[$path] = array(
					'local'			=> $local,
					'filter'		=> $filter,
					'attributes'	=>$attributes
			);
			parent::addCss($path,$local,$filter,$attributes);
		}
	}
	
	public function requireJs($path, $local = true, $filter = true, $attributes = null){
		if(!array_key_exists($path, $this->stackJs)){
			$this->stackJs[$path] = array(
					'local'			=> $local,
					'filter'		=> $filter,
					'attributes'	=>$attributes
			);
			parent::addJs($path,$local,$filter,$attributes);
		}
	}
	
	/*************************************************************************/
	// MODULE DIRECTORY
	/*************************************************************************/
	public function addModuleCss ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->moduleUri . $path ;
		$this->addCss($path,$local,$filter,$attributes);
	}
	
	public function addModuleJs ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->moduleUri . $path ;
		$this->addJs($path,$local,$filter,$attributes);
	}
	
	public function requireModuleCss($path, $local = true, $filter = true, $attributes = null){
		$path = $this->moduleUri . $path ;
		$this->requireCss($path,$local,$filter,$attributes);
	}
	
	public function requireModuleJs($path, $local = true, $filter = true, $attributes = null){
		$path = $this->moduleUri . $path ;
		$this->requireJs($path,$local,$filter,$attributes);
	}
	
	/*************************************************************************/
	// PATHS
	/*************************************************************************/
	
	public function getPath($directory){
		//return $this->siteBaseUri.$this->assetsUri.$directory.'/' ;
		return $this->assetsUri.$directory.'/' ;
	}
	
	public function getPathLib($resource){
		return $this->libUri.$resource  ;
	}
	
	public function getPathModules($moduleName,$resource = ''){
		return $this->getPath('modules').$moduleName.'/'.$resource ;
	}
	
	public function getPathTheme($resource){
		return $this->getPath('themes/'.Service::get(Service::THEME_NAME)).$resource ;
	}
	
	public function getPathUploads($date,$resource = '',$dateIsDirectory = false){
		if(!$dateIsDirectory){
			try {
				$date = new \DateTime($date);
				$dir = $date->format('Y/m/d');
			}catch (\Exception $e){
				$dir = $date ;
			}
		}else{
			$dir = $date ;
		}
		return $this->getPath('uploads').$dir.'/'.$resource ;
	}
	
	/*************************************************************************/
	// CUSTOM JS / CSS - LIB DIRECTORY
	/*************************************************************************/
	public function requireCustomJs($path, $local = true, $filter = true, $attributes = null){
		$path = $this->getPathLib($path) ;
		if(!array_key_exists($path, $this->stackCustomJs)){
			$this->stackCustomJs[$path] = array(
					'resource'	=> basename($path),
					'local'		=> $local,
					'filter'	=> $filter,
					'attributes'=> $attributes
			);
			$this->collection('customjs')->addJs($path, $local, $filter, $attributes);
		}
	}
	
	public function requireCustomCss($path, $local = true, $filter = true, $attributes = null){
		$path = $this->getPathLib($path) ;
		if(!array_key_exists($path, $this->stackCustomCss)){
			$this->stackCustomCss[$path] = array(
					'resource'	=> basename($path),
					'local'		=> $local,
					'filter'	=> $filter,
					'attributes'=> $attributes
			);
			$this->collection('customcss')->addCss($path, $local, $filter, $attributes);
		}
	}
	
	public function outputCustomJs(){
		$collection = 'customjs' ;
		return $this->outputJs($collection);
	}
	
	public function outputCustomCss(){
		$collection = 'customcss' ;
		return $this->outputCss($collection);
	}
}