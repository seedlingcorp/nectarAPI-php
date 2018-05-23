<?php

	//SET ERRORS
	ini_set('display_errors', 1);
	error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING|E_DEPRECATED));
	
	if(!function_exists('pr')){
		function pr($data = null){
			$trace = debug_backtrace()[0];
			echo '<b>File: </b>'.$trace['file'].' <b>Line: </b>'.$trace['line'];
			echo '<pre>'; !$data ? var_dump($data) : print_r($data);echo '</pre>';
		}
	}

	spl_autoload_register(function($class_name){
    	$class_name = substr(str_replace('\\', '/', $class_name), 7);
    	include __DIR__.'/../'.$class_name.'.php';
    });