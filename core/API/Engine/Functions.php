<?php

if(!function_exists('boolval')) {
    function boolval($BOOL, $STRICT=false) {

        if(is_string($BOOL)) {
            $BOOL = strtoupper($BOOL);
        }

        // no strict test, check only against false bool
        if( !$STRICT && in_array($BOOL, array(false, 0, NULL, 'FALSE', 'NO', 'N', 'OFF', '0'), true) ) {

            return false;

        // strict, check against true bool
        } elseif($STRICT && in_array($BOOL, array(true, 1, 'TRUE', 'YES', 'Y', 'ON', '1'), true) ) {

            return true;

        }

        // let PHP decide
        return $BOOL ? true : false;
    }
}

function rcopy($src,$dest,$cached = false){
	
	/*var_dump($src);
	var_dump('dirname '.dirname($src));*/
	
	/*var_dump($dest);
	var_dump('dirname '.dirname($dest));*/
	
	if(!is_dir($src)) return false;
	if(!is_dir($dest)) {
		$cached = false ;
		if(!mkdir($dest)) {
			return false;
		}
	}
	$i = new DirectoryIterator($src);
	foreach($i as $f) {
		if($f->isFile()) {
			if($cached){
				if(file_exists("$dest/".$f->getFilename())){
					
					$destTime = filemtime("$dest/".$f->getFilename());
					$srcTime = filemtime($f->getRealPath());
					$diff = $srcTime-$destTime;
				
					if($diff < 0)continue;
					var_dump('copy file');
				}
				
			}
			copy($f->getRealPath(), "$dest/" . $f->getFilename());
		} else if(!$f->isDot() && $f->isDir()) {
			rcopy($f->getRealPath(), "$dest/$f",$cached);
		}
	}
}

$dump = true ;
function enableDump($value){
	global $dump ;
	$dump = boolval($value);
}
function dump($expression){
	global $dump ;
	if($dump)var_dump($expression);
}