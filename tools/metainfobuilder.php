<?php

	require_once 'cli.php';

	function usage() {
        $usage = new Usage(
            'Meta Information Builder',
            'php ' . basename(__FIlE__),
            'Generates a static class with meta information about PHP MVC, such as version number, author, build date, etc.',
            'Tommy Montgomery',
            '2009'
        );
       
        global $switches;
        $usage->setSwitches($switches);
       
        echo $usage;
    }
	
	global $switches;
	$switches = new CliSwitchCollection();
	$switches->addSwitch(new CliSwitch('target',  't',  true,  'file',      'Where to write the output'))
	         ->addSwitch(new CliSwitch('name',    'n',  true,  'name',      'Product name'))
	         ->addSwitch(new CliSwitch('version', 'v',  true,  'version #', 'Product version'))
	         ->addSwitch(new CliSwitch('author',  'a',  true,  'name',      'Product author'))
	         ->addSwitch(new CliSwitch('website', 'w',  true,  'site',      'Product website'))
	         ->addSwitch(new CliSwitch('since',   's',  true,  'version #', 'Value for @since tag in documentation'))
	         ->addSwitch(new CliSwitch('help',    'h',  false, null,        'Display this help message (also --usage)'))
	         ->addSwitch(new CliSwitch('usage',   null, false, null,        'Display this help message (also --help)'));

	array_shift($argv);
	$args = Cli::parseArgs($argv, $switches);
	
	$options = array_map('addslashes', $args['switches']);
	$date    = date('Y-m-d H:i:s P');
	
	if (isset($options['help']) || isset($options['usage'])) {
		usage();
		exit(0);
	}
	
	$code = <<<CODE
<?php

	/**
	 * @package    PhpMvc
	 * @subpackage Utilities
	 * @since      $options[since]
	 * @copyright  © 2009 Tommy Montgomery
	 * @link       $options[website]
	 */
	
	namespace PhpMvc\Util;
	
	/**
	 * Provides meta information about PHP MVC
	 *
	 * @package    PhpMvc
	 * @subpackage Utilities
	 * @since      $options[since]
	 */
	final class MetaInfo {
		
		//@codeCoverageIgnoreStart
		/**
		 * @ignore
		 */
		private function __construct() {}
		//@codeCoverageIgnoreEnd
		
		/**
		 * The product version
		 *
		 * @var string
		 */
		const VERSION    = '$options[version]';
		
		/**
		 * The product author
		 *
		 * @var string
		 */
		const AUTHOR     = '$options[author]';
		
		/**
		 * The full product name
		 *
		 * @var string
		 */
		const NAME       = '$options[name]';
		
		/**
		 * The build date (Y-m-d H:i:s P)
		 *
		 * @var string
		 */
		const BUILD_DATE = '$date';
		
	}

?>
CODE;

	if (!file_put_contents($options['target'], $code)) {
		exit(1);
	}
	
	exit(0);

?>