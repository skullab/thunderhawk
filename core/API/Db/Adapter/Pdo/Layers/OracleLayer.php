<?php

namespace Thunderhawk\API\Db\Adapter\Pdo\Layers;

use Thunderhawk\API\Db\Adapter\Pdo\PdoInterface;
abstract class OracleLayer extends \Phalcon\Db\Adapter\Pdo\Oracle implements PdoInterface{

	public function dbExists() {}
	public function createDb() {}

}