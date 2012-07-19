<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class Twiggy_tools {

//my little addition for loading all helper functions at once

	function get_defined_functions_in_helper($helper) {
		if (file_exists("system/helpers/" . $helper . ".php")){//a native helper?
			$file = "system/helpers/" . $helper . ".php";
		}
		if (file_exists("application/helpers/" . $helper . ".php")){//a custom helper?
			$file = "application/helpers/" . $helper . ".php";
		}
		$source = file_get_contents($file);
		$tokens = token_get_all($source);

		$functions = array();
		$nextStringIsFunc = false;
		$inClass = false;
		$bracesCount = 0;

		foreach ($tokens as $token) {
			switch ($token[0]) {
			case T_CLASS:
				$inClass = true;
				break;
			case T_FUNCTION:
				if (!$inClass)
					$nextStringIsFunc = true;
				break;

			case T_STRING:
				if ($nextStringIsFunc) {
					$nextStringIsFunc = false;
					$functions[] = $token[1];
				}
				break;

			// Anonymous functions
			case '(':
			case ';':
				$nextStringIsFunc = false;
				break;

			// Exclude Classes
			case '{':
				if ($inClass)
					$bracesCount++;
				break;

			case '}':
				if ($inClass) {
					$bracesCount--;
					if ($bracesCount === 0)
						$inClass = false;
				}
				break;
			}
		}

		return $functions;

	}

}