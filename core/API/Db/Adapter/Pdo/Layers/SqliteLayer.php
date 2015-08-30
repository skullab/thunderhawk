<?php

namespace Thunderhawk\API\Db\Adapter\Pdo\Layers;

use Thunderhawk\API\Db\Adapter\Pdo\PdoInterface;
abstract class SqliteLayer extends \Phalcon\Db\Adapter\Pdo\Sqlite implements PdoInterface{
	
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

	}

	//@override
	public function createTable($tableName, $schemaName, array $definition) {
	
		$createTableQuery="CREATE TABLE $tableName (";
	
		for ($i=0;$i<count($definition["columns"]);$i++) {
			$columnObj=$definition["columns"][$i];
			$name=$columnObj->getName();
	
			$numberCapableColumns=array(\Phalcon\Db\Column::TYPE_INTEGER, \Phalcon\Db\Column::TYPE_VARCHAR);
	
			switch ($columnObj->getType()) {
				case \Phalcon\Db\Column::TYPE_INTEGER: $type="INT"; break;
				case \Phalcon\Db\Column::TYPE_VARCHAR: $type="VARCHAR"; break;
				case \Phalcon\Db\Column::TYPE_TEXT: $type="TEXT"; break;
			}
	
			$primary=($columnObj->isPrimary())?"PRIMARY KEY":"";
	
			$notNull=($columnObj->isNotNull())?"NOT NULL":"";
			$autoIncrement=($columnObj->isAutoIncrement ())?"AUTOINCREMENT":"";
	
	
			$colon=($i<(count($definition["columns"])-1))?",":"";
	
			//http://www.sqlite.org/faq.html#q1
			//Short answer: A column declared INTEGER PRIMARY KEY will autoincrement.
			//INTEGER(10) is wrong INTEGER IS WITHOUT NUMBER AND ONLY THIS CAN BE AUTOINCREMENTED
			//AUTOINCREMENT CAN BE ADDED
			//INT(10) is ok
			if ($columnObj->isPrimary()&&$type=="INT"&&$columnObj->isAutoIncrement()) {
				$type="INTEGER";
			}
	
	
			if (in_array($columnObj->getType(), $numberCapableColumns)&&$columnObj->getSize()>0
					&&$type!="INTEGER"
			) {
				$type=$type."(".$columnObj->getSize().")";
			}
	
	
	
	
			$createTableQuery.="
			$name $type $primary $autoIncrement $notNull $colon
			";
		}
	
	
		$createTableQuery.="\n);";
	
		return $this->execute($createTableQuery);
	
	
	}
	

}