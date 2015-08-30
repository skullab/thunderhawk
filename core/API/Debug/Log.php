<?php

namespace Thunderhawk\API\Debug;

class Log {
	
	private static $active = false ;
	private static $history = array();
	private static $filterTag = null ;
	private static $filterPrevious = null ;
	private static $filterOn = false ;
	private static $session = false ;
	private static $sessionBuffer = array() ;
	private static $enableBacktrace = false ;
	
	const INFO 		= 'info' ;
	const WARN		= 'warn' ;
	const ERR		= 'err' ;
	const LOG 		= 'log' ;
	
	public static function active($value,$filterTag = null,$backtrace = false){
		self::$active = boolval($value) ;
		self::filter($filterTag);
		self::$enableBacktrace = boolval($backtrace) ;
	}
	
	public static function enableBacktrace($value){
		self::$enableBacktrace = boolval($value);
	}
	
	public static function timestamp($format = 'Y-m-d H:i:s'){
		return '['.date($format).substr((string)microtime(), 1, 8).']';
	}
	
	public static function send($type,$tag,$expression){
		if(!self::$active)return;
		
		
		$var = json_encode(var_export($expression,true));
		if(!array_key_exists($tag, self::$history))self::$history[$tag] = array() ;
		$put = array(
				'timestamp' 	=> self::timestamp(),
				'content'		=> $var,
				'backtrace'		=> json_encode(debug_backtrace())
				
		);
		array_push(self::$history[$tag], $put);
		
		if(!self::$session && self::$filterOn && $tag != self::$filterTag)return ;
		
		if(!self::$session)ob_start();
		
		if(self::$session && $tag != self::$filterTag)return;
		
		echo '<script type="text/javascript">' ;
		switch ($type){
			case self::INFO :
				echo 'console.info("'.$put['timestamp'].'","'.$tag.' : ",'.$var.');';
				break;
			case self::WARN :
				echo 'console.warn("'.$put['timestamp'].'","'.$tag.' : ",'.$var.');';
				break;
			case self::ERR :
				echo 'console.error("'.$put['timestamp'].'","'.$tag.' : ",'.$var.');';
				break;
			default:
				echo 'console.log("'.$put['timestamp'].'","'.$tag.' : ",'.$var.');';
				break;
		}
		if(self::$enableBacktrace){
			echo 'console.dir('.$put['backtrace'].');';
		}
		echo '</script>' ;
		
		
		if(!self::$session){
			$out = ob_get_contents();
			ob_end_clean();
			echo $out ;
		}
	}
	
	public static function format(\Exception $e){
		$output = get_class($e) . ": ". $e->getMessage(). "\n" .
    	" File = " . $e->getFile(). "\n" .
    	" Line = " . $e->getLine(). "\n" .
    	$e->getTraceAsString();
		return $output ;
	}
	
	public static function I($tag,$expression){
		self::send(self::INFO, $tag, $expression);
	}
	public static function W($tag,$expression){
		self::send(self::WARN,$tag,$expression);
	}
	public static function E($tag,$expression){
		self::send(self::ERR, $tag, $expression);
	}
	public static function L($tag,$expression){
		self::send(self::LOG, $tag, $expression);
	}
	
	public static function filter($tag = null){
		if(self::$session)return;
		self::$filterOn = ($tag == null) ? false : true ;
		self::$filterTag = $tag ;
	}
	
	public static function sessionStart($tag = null){
		if(!self::$active || $tag == null)return ;
		
		if(!self::$session){
			self::$session = true ;
			self::$filterPrevious = self::$filterTag ;
			self::$filterTag = $tag ;
			ob_start('self::sessionOutput');
		}
		
	}
	public static function sessionEnd(){
		if(!self::$active || !self::$session)return;
		self::$session = false ;
		ob_end_clean();
		echo self::$sessionBuffer[self::$filterTag] ;
		self::$filterTag = self::$filterPrevious ;
	}
	private static function sessionOutput($buffer){
		self::$sessionBuffer[self::$filterTag] = $buffer ;
	}
	
	public static function getHistory(){
		return self::$history ;
	}
	
}