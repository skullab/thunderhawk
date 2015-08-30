<?php
namespace Thunderhawk\API\Db\Adapter\Pdo\Layers;
use Thunderhawk\API\Db\Adapter\Pdo\PdoInterface;

abstract class MysqlLayer extends \Phalcon\Db\Adapter\Pdo\Mysql implements PdoInterface{

	private $_dbExist ;
	private $_mDescriptor ;
	private $_mHostInfo ;
	
	public function __construct(array $descriptor){
		$this->_mDescriptor = $descriptor ;
		try {
			$this->_dbExist = true ;
			parent::__construct ( $descriptor );
		} catch ( \Exception $e ) {
			//DB not exists !
			if ($e->getCode () == 1049) {
				$this->_dbExist = false;
			}
		}
	}
	
	public function dbExists() {
		return $this->_dbExist ;
	}

	public function createDb() {
		if (! $this->_dbExist) {
			$mysqli = new \mysqli (
					$this->_mDescriptor ['host'],
					$this->_mDescriptor ['username'],
					$this->_mDescriptor ['password'] );
				
			if ($mysqli->connect_error) {
				die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
			}
				
			$this->_mHostInfo = $mysqli->host_info ;
				
			$sql = 'CREATE DATABASE IF NOT EXISTS '.$this->_mDescriptor['dbname'] ;
				
			if(!$mysqli->query($sql)){
				$mysqli->close();
				die("DB creation failed: (" . $mysqli->errno . ") " . $mysqli->error);
			}
				
			$mysqli->close();
			$this->_dbExist = true ;
				
			parent::__construct ( $this->_mDescriptor);
		}
	}

}