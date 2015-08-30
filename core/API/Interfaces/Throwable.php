<?php

namespace Thunderhawk\API\Interfaces;
interface Throwable {
	public static function throwException($message = null , $code = 0 , \Exception $previous = null);
}