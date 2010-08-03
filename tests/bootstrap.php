<?php

	namespace Phinq\Tests;

	require_once 'PHPUnit/Framework.php';
	require_once 'PHPUnit/Extensions/OutputTestCase.php';
	require_once dirname(__DIR__) . '/src/Phinq/bootstrap.php';
	
	spl_autoload_register(function($class) {
		if (preg_match('/Phinq\\\Tests\\\.+/', $class)) {
			require_once __DIR__ . '/helpers.php';
		}
	});

?>