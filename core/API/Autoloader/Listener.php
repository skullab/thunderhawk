<?php

namespace Thunderhawk\API\Autoloader;

class Listener {
	public function beforeCheckClass($event,$loader){
		//dump('loader : beforeCheckClass');
	}
	public function pathFound($event,$loader){
		//dump('loader : pathFound');
		//dump($event->getData(),$loader);
	}
	public function afterCheckClass($event,$loader){
		//dump('loader : afterCheckClass');
	}
}