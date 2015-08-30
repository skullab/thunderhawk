<?php
namespace Thunderhawk\API;
use Thunderhawk\API\Interfaces\Throwable;
use \Phalcon\Mvc\Router\Route as Route ;

class Manifest extends \SimpleXMLElement implements Throwable{
	
	const TAG_MODULE 			= 'Module';
	const TAG_APPLICATION		= 'Application';
	const TAG_NAMESPACE			= 'namespace';
	const TAG_NAME				= 'name';
	const TAG_ROUTING			= 'routing';
	const TAG_ROUTE				= 'route';
	const TAG_VERSION			= 'version';
	const TAG_RELEASE			= 'release';
	const TAG_MAJOR				= 'major';
	const TAG_MINOR				= 'minor';
	const TAG_CODE				= 'code';
	const TAG_PERMISSIONS		= 'permissions';
	const TAG_PERMISSION		= 'permission';
	const TAG_GROUP				= 'group';
	const TAG_RULE				= 'rule';
	const TAG_REQUIRED			= 'required';
	const TAG_REQUIRE			= 'require';
	const TAG_REQUIRED_MODULE	= 'module';
	const TAG_PLUGIN			= 'plugin';
	const TAG_TEMPLATE			= 'template';
	const TAG_ENGINE			= 'engine';
	const ATTRIBUTE_MODULE		= 'module';
	const ATTRIBUTE_PLUGIN		= 'plugin';
	const ATTRIBUTE_COMPONENT	= 'component';
	const ATTRIBUTE_CONTROLLER 	= 'controller';
	const ATTRIBUTE_ACTION		= 'action';
	const ATTRIBUTE_PARAMS		= 'params';
	const ATTRIBUTE_NAME		= 'name';
	const ATTRIBUTE_NAMESPACE	= 'namespace';
	const ATTRIBUTE_HTTP_METHODS= 'http-methods' ;
	
	const VALUE_INDEX			= 'index';
	const VALUE_GET				= 'GET' ;
	const VALUE_POST			= 'POST';
	const VALUE_PUT				= 'PUT';
	const VALUE_PATCH			= 'PATCH';
	const VALUE_DELETE			= 'DELETE';
	const VALUE_OPTIONS			= 'OPTIONS';
	const VALUE_HEAD			= 'HEAD';
	
	
	public function addAttribute($name, $value = null, $namespace = null) {/*do nothing*/}
	public function addChild($name, $value = null, $namespace = null) {/*do nothing*/}
	
	public function validate(){
		$document = new \DOMDocument();
		$document->loadXML($this->asXML());
		$filename = 'path/to/schema.xsd';
		return $document->schemaValidate($filename);
	}
	
	public function getTag($tag){
		return (string)$this->$tag ;
	}
	
	public function getModuleName(){
		return $this->getTag(self::TAG_NAME);
	}
	
	public function getModuleNamespace(){
		return $this->getTag(self::TAG_NAMESPACE);
	}
	
	public function getVersion(){
		$version = (string)($this->version->release . '.' . $this->version->major . '.' . $this->version->minor) ;
		return $version ;
	}
	
	public function getVersionInt($part){
		return (int)$this->version->$part ;	
	}
	
	public function hasRouting(){
		return isset($this->routing);
	}
	
	public function hasPermissions(){
		return isset($this->permissions);
	}
	
	public function hasRequired(){
		return isset($this->required);
	}
	public function hasTemplate(){
		return isset($this->template);
	}
	public function isModule(){
		return $this->getName() === self::TAG_MODULE ;
	}
	
	public function isApplication(){
		return $this->getName() === self::TAG_APPLICATION ;
	}
	
	public function getRoutes(){
		return $this->hasRouting() ? $this->_getRoutes() : null ;
	}
	
	private function _getRoutes(){
		$routes = array();
		foreach ($this->routing->route as $route){
			
			$paths = array(
				self::ATTRIBUTE_NAMESPACE	=> $this->getModuleNamespace().'\Controllers',
				self::ATTRIBUTE_MODULE 		=> $this->getModuleName(),
				self::ATTRIBUTE_CONTROLLER	=> self::VALUE_INDEX,
				self::ATTRIBUTE_ACTION		=> self::VALUE_INDEX,
			);
			$pattern = '/' . trim((string)$route);
			//var_dump($pattern);
			foreach ($route->attributes() as $attribute => $value){
				if($attribute === self::ATTRIBUTE_HTTP_METHODS || $attribute === self::ATTRIBUTE_NAME)continue;
				$value = (string)$value;
				$paths[(string)$attribute] = is_numeric($value) ? (int)$value : $value ; 
			}
			if($paths[self::ATTRIBUTE_MODULE] != $this->getModuleName()){
				unset($paths[self::ATTRIBUTE_NAMESPACE]);
			}
			$Route = new Route($pattern,$paths);
			if(isset($route[self::ATTRIBUTE_HTTP_METHODS])){
				$httpMethods = explode(',',(string)$route[self::ATTRIBUTE_HTTP_METHODS]);
				$Route->via($httpMethods);
			}
			if(isset($route[self::ATTRIBUTE_NAME])){
				$Route->setName((string)$route[self::ATTRIBUTE_NAME]);
			}
			array_push($routes, $Route);
		}
		return $routes ;
	}
	
	public function getPermissions(){
		return $this->hasPermissions() ? $this->_getPermissions() : null ;
	}
	
	private function _getPermissions(){
		
		$permissions = array(
				'permissions'	=> array(),
				'groups'		=> array()
		);
		
		foreach($this->permissions->group as $group){
			array_push($permissions['groups'], (string)$group);
		}
		
		foreach ($this->permissions->permission as $permission){
			array_push($permissions['permissions'], (string)$permission);
		}
		
		return $permissions ;
	}
	
	public function getRequired(){
		return $this->hasRequired() ? $this->_getRequired() : null ;
	}
	
	private function _getRequired(){
		$required = array();
		
		foreach($this->required->require as $require){
			$directives = array() ;
			foreach ($require->attributes() as $attribute => $value){
				$directives[(string)$attribute] = (string)$value ;
			}
			array_push($required, $directives);
		}
		
		return $required ;
	}
	
	public function getTemplateEngines(){
		return $this->hasTemplate() ? $this->_getTemplateEngines() : null ;
	}
	
	private function _getTemplateEngines(){
		$engines = array() ;
		foreach ($this->template->engine as $engine){
			$directives = array('engine' => (string)$engine);
			foreach ($engine->attributes() as $attribute => $value){
				$directives[(string)$attribute] = (string)$value;
			}
			array_push($engines, $directives);
		}
		return $engines ;
	}
	
	/* (non-PHPdoc)
	 * @see \Thunderhawk\core\engine\interfaces\Throwable::throwException()
	 */
	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		
	}

}