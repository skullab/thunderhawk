<?php

namespace Thunderhawk\API\Engine;

use Thunderhawk\API\Engine;
use Thunderhawk\API\Tokenizer;

final class Constants {
	
	private static $_alreadyInit = false ;
	
	public function __construct(){
		if(self::$_alreadyInit)return;
		
		define('THUNDERHAWK','Thunderhawk');
		define('TH',THUNDERHAWK);
		define('TH_VERSION',Engine::getVersion());
		define('TH_PREFIX','th');
		define('TH_DB_PREFIX',Engine::getInstance()->getDbPrefix().'_');
		define('TH_BASE_DIR',$_SERVER['DOCUMENT_ROOT'].Engine::getInstance()->getBaseUri());
		
		self::$_alreadyInit = true;
	}
	
}