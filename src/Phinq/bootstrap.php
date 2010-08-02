<?php

	/**
	 * Bootstrapper for Phinq
	 *
	 * @package   Phinq
	 * @since     1.0
	 */

	namespace Phinq;

	spl_autoload_register(function($class) {
		$file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php';
		
		if (is_file($file)) {
			require_once $file;
		}
	});

?>