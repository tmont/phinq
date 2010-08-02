<?php

	namespace Phinq;

	interface Expression {
		function invoke(array $collection);
	}
	
?>
