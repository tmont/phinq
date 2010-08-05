<?php

	require_once 'cli.php';

	function usage() {
        $usage = new Usage(
            'Phar Builder',
            'php ' . basename(__FIlE__),
            'Generates a Phar archive of the PHP MVC framework',
            'Tommy Montgomery',
            '2010'
        );
       
        global $switches;
        $usage->setSwitches($switches);
       
        echo $usage;
    }
	
	if (!extension_loaded('phar')) {
		trigger_error('The phar extension is required', E_USER_ERROR);
	}
	if (!Phar::canWrite()) {
		trigger_error('phar.readonly must be set to 0 in php.ini', E_USER_ERROR);
	}
	
	global $switches;
	$switches = new CliSwitchCollection();
	$switches->addSwitch(new CliSwitch('dir',       'd',  true,  'directory', 'The directory to insert into the phar'))
	         ->addSwitch(new CliSwitch('recursive', 'r',  false, null,        'Whether to recurse into subdirectories'))
	         ->addSwitch(new CliSwitch('stub',                's',  false, 'file',      'Relative path to stub file'))
	         ->addSwitch(new CliSwitch('help',      'h',  false, null,        'Display this help message (also --usage)'))
	         ->addSwitch(new CliSwitch('usage',     null, false, null,        'Display this help message (also --help)'))
	         ->addSwitch(new CliSwitch(null,        null, true, '<file>',     'The target phar file'));

	array_shift($argv);
	$args = Cli::parseArgs($argv, $switches);
	
	$target = array_shift($args['args']);
	$options = $args['switches'];
	
	if (isset($options['help']) || isset($options['usage'])) {
		usage();
		exit(0);
	}
	
	if (!isset($options['dir'])) {
		fwrite(STDERR, '--dir is a required argument');
		exit(1);
	}
	if (!isset($target)) {
		fwrite(STDERR, 'No target phar file given');
		exit(1);
	}
	
	$dir = realpath($options['dir']);
	$prefix = $dir;

	$iterator = isset($options['recursive']) ? new RecursiveDirectoryIterator($dir) : new DirectoryIterator($dir);
	
	class NegativeIterator extends RecursiveFilterIterator {
		
		public function accept() {
			$file = $this->getInnerIterator()->current();
			return !preg_match('@\.svn@', $file->getPathName());
		}
		
	}
	
	$iterator = new NegativeIterator($iterator);
	
	
	$phar = new Phar($target);
	$phar->buildFromIterator(new RecursiveIteratorIterator($iterator), $prefix);
	
	if (isset($options['stub'])) {
		$phar->setStub($phar->createDefaultStub($options['stub']));
	}
	
	$phar->compressFiles(Phar::GZ);
	exit(0);
	
?>