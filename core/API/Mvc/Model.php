<?php

namespace Thunderhawk\API\Mvc;

abstract class Model extends \Phalcon\Mvc\Model{
	
	private $moduleInstance = null ;
	private $modelName = null ;
	
	public function initialize(){
		$ref = new \ReflectionClass($this);
		$namespace = str_replace(basename($ref->getNamespaceName()),'',$ref->getNamespaceName());
		$this->moduleInstance = $this->getDI()->get($namespace.'Module');
		
		$this->modelName = strtolower(basename($ref->name)) ;
		$this->setSource(TH_DB_PREFIX.$this->modelName);
		
		$this->onInitialize();
	}
	
	protected function onInitialize(){}
}