<?php

namespace Thunderhawk\API\Exceptions;

abstract class BaseException extends \Exception {
	
	protected $extra = array();
	protected $messages = array(
			0 		=> 'Unknown exception' ,
	);
	
	public function __construct($message = null, $code = 0, \Exception $previous = null) {
		$this->defineMessages($this->messages,$this->extra);
		if(!isset($this->messages[$code]))$this->messages[$code] = 'Unknown exception' ;
		$mess = $message == null ? $this->messages[$code] : sprintf($this->messages[$code],$message);
		parent::__construct ( $mess, $code, $previous );
	}
	abstract protected function defineMessages(&$messages,$extra = []);
	 
	/* (non-PHPdoc)
	 * @see Exception::__toString()
	 */
	public function __toString() {
		//return parent::__toString();
		$ref = new \ReflectionClass($this);
		return '\''.$ref->getName().'\' with message \''.$this->message.'\' and code \''.$this->code.'\'' ;
	}

	
}