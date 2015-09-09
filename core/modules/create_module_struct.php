<?php
/**********************************************************************************************/
/* 									[CONFIGURATION]											  */

$config = array(
	'moduleDir'			=> 'Frontend',
	'namespace'			=> 'Thunderhawk\Modules\Frontend',
	'moduleName'		=> 'frontend',
	'info'				=> array(
			'author'	=> 'Thunderhawk',
			'description'	=> array(
					'long'	=> '',
					'short' => ''
			),
			
	),
	'version'			=> array(
			'release'	=> '0',
			'major'		=> '0',
			'minor'		=> '1'
	),
	'template'			=> array(
			'engine1'	=> array(
					'name'		=> 'php',
					'extension' => 'phtml'
			),
			'engine2'	=> array(
					'name'		=> 'volt',
					'extension' => 'volt'
			)
	),
	
	'permissionGroup'	=> array(
			'GROUP_BASE',
	),
	'permissions'		=> array(	
			'VOLT',
			'FLASH',
			'DB'
	),
);










/**********************************************************************************************/
createModuleStruct();
/**********************************************************************************************/
function createModuleStruct($n = 0){
	global $config ;
	
	$dir = $n > 0 ? $config['moduleDir'].$n : $config['moduleDir'];
	$namespace = $config['namespace'];
	$moduleName	= $n > 0 ? $config['moduleName'].$n : $config['moduleName'];
	$use = 'use Thunderhawk\API\Adapters\Module as ModuleAdapter;';
	
	if(!file_exists($dir)){
		mkdir($dir);
		mkdir($dir.'/controllers');
		mkdir($dir.'/modules');
		mkdir($dir.'/views');
		
		$module = fopen($dir.'/Module.php','w');
		fwrite($module,'<?php
namespace '.$namespace.';
				
'.$use.'

class Module extends ModuleAdapter{
}');
		
		fclose($module);
		
		foreach ($config['permissionGroup'] as $group){
			$permissionGroup = '<group>Service::'.$group.'</group>'.PHP_EOL ;
		}
		
		foreach ($config['permissions'] as $permission){
			$permissions .= '<permission>Service::'.$permission.'</permission>'.PHP_EOL ;
		}
		
		foreach ($config['template'] as $engine){
			$templates .= '<engine extension="'.$engine['extension'].'">'.$engine['name'].'</engine>'.PHP_EOL ;
		}
		
		$manifest = fopen($dir.'/Manifest.xml', 'w');
		fwrite($manifest,'<?xml version="1.0" encoding="UTF-8"?>
<Module>
	<namespace>'.$namespace.'</namespace>
	<name>'.$moduleName.'</name>
				
	<info>
		<author>'.$config['info']['author'].'</author>
		<description>
			<long>
				'.$config['info']['description']['long'].'
			</long>
			<short>'.$config['info']['description']['short'].'</short>
		</description>
	</info>

	<version>
		<release>'.$config['version']['release'].'</release>
		<major>'.$config['version']['major'].'</major>
		<minor>'.$config['version']['minor'].'</minor>
	</version>

	<template>
		'.$templates.'
	</template>
				
	<routing>
		<route>'.$moduleName.'</route>
	</routing>
	
	<required>
		
	</required>
				
	<permissions>
		'.$permissionGroup.'
		'.$permissions.'
	</permissions>
</Module>');
		
		fclose($manifest);
		
		echo 'Module "'.$dir.'" successfully created !';
	}else{
		createModuleStruct(++$n);
	}
}