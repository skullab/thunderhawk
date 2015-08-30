<?php

namespace Thunderhawk\API\Service;
use Thunderhawk\API\Exceptions\BaseException;

class Exception extends BaseException {
	
	protected function defineMessages(&$messages,$extra = []){
		$messages[200] = 'Service "%s" not found';
	}
}