<?php

namespace Thunderhawk\API\Permission;
use Thunderhawk\API\Exceptions\BaseException;
class Exception extends BaseException{
	
	protected function defineMessages(&$messages,$extra = []){
		$messages[100] = 'The name of permissions group must be a string' ;
		$messages[110] = 'The name of permissions group "%s" already exists';
		$messages[200] = 'The arguments of Permission\Group constructor must be instance of Permission'; 
	}
}