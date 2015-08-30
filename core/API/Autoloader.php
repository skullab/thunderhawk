<?php

namespace Thunderhawk\API ;
use \Phalcon\Loader as Loader ;
use \Phalcon\Config\Adapter\Ini as Config;
use Thunderhawk\API\Interfaces\Throwable ;
use Thunderhawk\API\Autoloader\Exception ;
use Thunderhawk\API\Autoloader\Listener;

require 'Interfaces/Throwable.php';

final class Autoloader extends Loader implements Throwable{
	
	private static $_alreadyInit = false ;
	private static $_token = null ;
	
	private $config = null ;
	private $newDbIniName = null ;
	
	public function __construct($token){
		if(!self::$_alreadyInit){
			self::$_alreadyInit = true ;
			self::$_token = $token ;
			parent::__construct();
			$this->registerConfiguration();
			$this->registerDefaultDirs();
			$this->registerDefaultNamespaces();
			$this->registerListener();
		}else self::throwException(null,100);
	}
	
	private function registerListener(){
		$eventsManager = new \Phalcon\Events\Manager();
		$eventsManager->attach('loader', new Listener());
		$this->setEventsManager($eventsManager);
	}
	
	private function registerConfiguration(){
		$backDir = '/../' ;
		$this->config = array();
		$this->config['app'] = new Config($backDir.'config/app.ini.php');
		$this->config['dir'] = new Config($backDir.'config/dir.ini.php');
		$this->config['db'] = new Config($backDir.'config/db.ini.php');
		$this->createDbName();
	}
	
	private function createDbName(){
		$dbname = $this->config['db']->database->default ;
		if(strpos($dbname,'_ver_') !== false){
			$dbname = str_replace('_ver_', '_'.str_replace('.', '_', Engine::getVersion()), $dbname);
		}
		$this->config['db']->database->dbname = $dbname ;
	}
	 
	private function registerDefaultDirs(){
		$backDir = '../' ;
		$this->registerDirs(array(
				$backDir.$this->config['dir']->core->modules,
				$backDir.$this->config['dir']->core->cache,
				$backDir.$this->config['dir']->core->logs,
				$backDir.$this->config['dir']->core->plugins
		),true)->register();
		//dump($this->getDirs());
	}
	private function registerDefaultNamespaces(){
		$backDir = '../' ;
		$this->registerNamespaces(array(
				'Thunderhawk\API' 	=> $backDir.$this->config['app']->API->base,
				'Thunderhawk\Modules'	=> $backDir.$this->config['dir']->core->modules,
		),true)->register();
		//dump($this->getNamespaces());
	}	
	public function getConfigDirs($basePath = null){
		$backDir = '/../' ;
		$dirs = new Config($backDir.'config/dir.ini.php');
		if($basePath == null)return $dirs;
		foreach ($dirs as $set => $obj){
			if($set != 'base' && $set != 'public')
			foreach ($dirs[$set] as $key => $value){
				$dirs[$set][$key] = $basePath.$value;
			}
		}
		return $dirs ;
	}
	
	public function getDbPrefix(){
		return $this->config['db']->database->prefix ;
	}
	
	public function getDbConnection($token){
		if($token === self::$_token){
			$adapter = 'Thunderhawk\API\Db\Adapter\Pdo\\' . $this->config['db']->database->adapter ;
			
			if($this->config['db']->database->dsn != null){
				return new $adapter(array(
						'dsn'	=> $this->config['db']->database->dsn,
						'username'	=> $this->config['db']->database->username,
						'password'	=> $this->config['db']->database->password
				));
			}else{
				return new $adapter(array(
						'host'		=> $this->config['db']->database->host,
						'username'	=> $this->config['db']->database->username,
						'password'	=> $this->config['db']->database->password,
						'dbname'	=> $this->config['db']->database->dbname
				));
			}
			
		}else self::throwException(null,200);
	}
	/* (non-PHPdoc)
	 * @see \Thunderhawk\core\engine\interfaces\Throwable::throwException()
	 */
	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Exception($message,$code,$previous);
	}

}
