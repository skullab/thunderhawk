<?php

namespace Thunderhawk\API\Permission;

use Thunderhawk\API\Permission;
use Thunderhawk\API\Interfaces\Throwable;

class Group implements \Iterator , Throwable{
	
	private $group = array();
	
	public function __construct(){
		$args = func_get_args();
		foreach ($args as $permission){
			if(!$permission instanceof Permission){self::throwException(null,200);}
			array_push($this->group, $permission);
		}
	}
	/* (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current() {
		return current($this->group);
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() {
		return key($this->group);
	}
	
	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() {
		return next($this->group);
	}
	
	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		return reset($this->group);
	}
	
	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {
		$key = $this->key();
		return ($key !== NULL && $key !== FALSE);
	}

	public function toArray(){
		return $this->group ;
	}
	
	public function inflate(Permission $permission){
		array_push($this->group, $permission);
		return $this ;
	}
	
	public function merge(Group $group){
		$this->group = array_merge($this->group,$group->toArray());
		return $this ;
	}
	
	/* (non-PHPdoc)
	 * @see \Thunderhawk\API\Interfaces\Throwable::throwException()
	 */
	public static function throwException($message = null, $code = 0, Exception $previous = null) {
		throw new Exception($message,$code,$previous);
	}

}