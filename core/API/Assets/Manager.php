<?php
namespace Thunderhawk\API\Assets;

use Thunderhawk\API\Autoloader;
use Thunderhawk\API\Engine;
use Thunderhawk\API\Service;
class Manager extends \Phalcon\Assets\Manager{
	
	private $siteBaseUri = ''; // like url base uri
	private $baseUri = '' ; // base uri for module
	
	private $assetsUri = '';
	private $standardUri = '' ;
	private $themesUri	= '';
	private $libUri	= '';
	private $jqueryUri = '' ;
	private $jqueryuiUri = '' ;
	
	private $stackCss = array();
	private $stackJs = array();
	private $stackStandardCss = array();
 	private $stackStandardJs = array();
 	private $stackJQuery = array();
 	private $stackJQueryUi = array();
 	
 	private $stackCustomJs = array();
 	private $stackCustomCss = array();
 	
	public function __construct($options = null){
		parent::__construct($options);
		if(array_key_exists('baseUri', $this->getOptions())){
			$this->baseUri = $this->getOptions()['baseUri'];
		}
		
		$this->siteBaseUri = Service::get(Service::URL)->getBaseUri();
		$dirs = Engine::getInstance()->getConfigDirs();
		$this->assetsUri = $dirs['public']->assets ;
		$this->standardUri = $dirs['assets']->standard ;
		$this->themesUri = $dirs['assets']->themes ;
		$this->libUri = $dirs['assets']->lib ;
		$this->jqueryUri = $dirs['lib']->jquery ;
		$this->jqueryuiUri = $dirs['lib']->jqueryui ;
		
		$this->collection('jquery');
		$this->collection('jqueryui');
		$this->collection('jqueryuijs');
		
		$this->collection('customjs');
		$this->collection('customcss');
	}
	
	public function addCss ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->baseUri . $path ;
		$this->stackCss[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=>$attributes
		);
		parent::addCss($path,$local,$filter,$attributes);
	}
	
	public function addJs ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->baseUri . $path ;
		$this->stackJs[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=>$attributes
		);
		parent::addJs($path,$local,$filter,$attributes);
	}
	
	public function addStandardCss ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->standardUri . $path ;
		$this->stackStandardCss[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=>$attributes
		);
		parent::addCss($path,$local,$filter,$attributes);
	}
	
	public function addStandardJs ($path, $local = true, $filter = true, $attributes = null){
		$path = $this->standardUri . $path ;
		$this->stackStandardJs[$path] = array(
				'local'			=> $local,
				'filter'		=> $filter,
				'attributes'	=>$attributes
		);
		parent::addJs($path,$local,$filter,$attributes);
	}
	
	public function requireCss($path, $local = true, $filter = true, $attributes = null){
		$path = $this->baseUri . $path ;
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
		$path = $this->baseUri . $path ;
		if(!array_key_exists($path, $this->stackJs)){
			$this->stackJs[$path] = array(
					'local'			=> $local,
					'filter'		=> $filter,
					'attributes'	=>$attributes
			);
			parent::addJs($path,$local,$filter,$attributes);
		}
	}
	
	public function requireStandardCss($path, $local = true, $filter = true, $attributes = null){
		$path = $this->standardUri . $path ;
		if(!array_key_exists($path, $this->stackStandardCss)){
			$this->stackStandardCss[$path] = array(
					'local'			=> $local,
					'filter'		=> $filter,
					'attributes'	=>$attributes
			);
			parent::addCss($path,$local,$filter,$attributes);
		}
	}
	
	public function requireStandardJs($path, $local = true, $filter = true, $attributes = null){
		$path = $this->standardUri . $path ;
		if(!array_key_exists($path, $this->stackStandardJs)){
			$this->stackStandardJs[$path] = array(
					'local'			=> $local,
					'filter'		=> $filter,
					'attributes'	=>$attributes
			);
			parent::addJs($path,$local,$filter,$attributes);
		}
	}
	
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
	
	public function getPath($directory){
		return $this->siteBaseUri.$this->assetsUri.$directory.'/' ;
	}
	
	public function getPathLib($resource){
		return $this->libUri.$resource  ;
	}
	
	public function getPathModules($moduleName,$resource = ''){
		return $this->getPath('modules').$moduleName.'/'.$resource ;
	}
	
	public function getPathStandard($resource){
		return $this->getPath('standard').$resource ;
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
	
	public function outputCustomJs(){
		$collection = 'customjs' ;
		return $this->outputJs($collection);
	}
	
	public function outputCustomCss(){
		$collection = 'customcss' ;
		return $this->outputCss($collection);
	}
	
	/*************************************************************************/
	public function outputJQuery($version = false){
		$collection = $version === false ? 'jquery' : 'jquery-'.$version ;
		return $this->outputJs($collection);
	}
	
	public function outputJQueryUI($version = false){
		$collection = $version === false ? 'jqueryui' : 'jqueryui-'.$version ;
		$collectionjs = $version === false ? 'jqueryuijs' : 'jqueryuijs-'.$version ;
		
		$this->outputCss($collection);
		$this->outputJs($collectionjs);
	}
	
	public function requireJQuery($version = 'default',$cdn = false){
		
		$path =  $this->jqueryUri ;
		if($cdn){
			$path = $version == 'default' ? '//code.jquery.com/jquery.js' : '//code.jquery.com/jquery-' . $version . '.js' ;
		}else{
			$path .= $version == 'default' ? 'jquery.js' : 'jquery-' . $version . '.js' ;
		}
		if(!array_key_exists($path, $this->stackJQuery)){
			$min = strpos($version, 'min');
			$filter = $min === false ? true : false ;
			$this->stackJQuery[$path] = array(
					'version'	=> $version,
					'min'		=> !$filter,
					'cdn'		=> $cdn
			);
			$this->collection('jquery')->addJs($path,!$cdn,$filter,null);
			$this->collection('jquery-'.$version)->addJs($path,!$cdn,$filter,null);
		}
	}
	
	public function requireJQueryCDN($version = null){
		$this->requireJQuery($version,true);
	}
	
	public function requireJQueryUI($version = 'default',$min = true){
		$path = $this->jqueryuiUri.$version.'/' ;
		$pathjs = $path ;
		$path .= $min ? 'jquery-ui.min.css' : 'jquery-ui.css' ;
		$pathjs .= $min ? 'jquery-ui.min.js' : 'jquery-ui.js' ;
		
		$filter = !$min ;
		if(!array_key_exists($path, $this->stackJQueryUi)){
			$this->stackJQueryUi[$path] = array(
					'version'	=> $version,
					'min'		=> $min,
					'js'		=> basename($pathjs)
			);
		}
		
		$this->collection('jqueryui')->addCss($path,true,$filter,null);
		$this->collection('jqueryui-'.$version)->addCss($path,true,$filter,null);
		
		$this->collection('jqueryuijs')->addJs($pathjs,true,$filter,null);
		$this->collection('jqueryuijs-'.$version)->addJs($pathjs,true,$filter,null);
		
	}
}