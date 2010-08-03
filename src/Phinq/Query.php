<?php

	namespace Phinq;

	interface Query {
		function execute(array $collection);
	}
	
?>
