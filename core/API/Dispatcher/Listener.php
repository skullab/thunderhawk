<?php

namespace Thunderhawk\API\Dispatcher;

class Listener {
	
	public function beforeDispatchLoop($event, $dispatcher) {
		dump ( 'before dispatch loop' );
	}
	public function beforeDispatch($event, $dispatcher) {
		dump ( 'before dispatch' );
	}
	public function beforeExecuteRoute($event, $dispatcher) {
		dump ( 'before execute route' );
	}
	
	public function afterExecuteRoute($event, $dispatcher) {
		dump ( 'after execute route' );
	}
	public function beforeNotFoundAction($event, $dispatcher) {
		dump ( 'before not found action' );
		//dump($dispatcher->getActionName());
	}
	public function beforeException($event, $dispatcher, $exception) {
		dump ( 'before exception' );
		
		if ($exception instanceof \Phalcon\Mvc\Dispatcher\Exception) {
			switch ($exception->getCode()) {
				case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
				case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
					$dispatcher->forward(array(
						'controller'=> 'index',
						'action' 	=> 'error',
						'params'	=> array(404,'page not found')
					));
					return false;
			}
		}
		
		return true ;
	}
	
	public function afterDispatch($event, $dispatcher) {
		dump ( 'after dispatch' );
	}
	public function afterDispatchLoop($event, $dispatcher) {
		dump ( 'after dispatch loop' );
	}
}