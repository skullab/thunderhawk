<?php
namespace Thunderhawk\API\Autoloader;
use Thunderhawk\API\Exceptions\BaseException;

class Exception extends BaseException{
	
	/* (non-PHPdoc)
	 * @see \Thunderhawk\core\engine\exceptions\BaseException::defineMessages()
	 */
	protected function defineMessages(&$messages,$extra = []) {
		$messages[100] = 'Autoloader is already instantiated';
		$messages[200] = 'Secure hash is incorrect' ;
	}

}