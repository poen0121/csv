<?php
if (!class_exists('hpl_func_arg')) {
	include (strtr(dirname(__FILE__), '\\', '/') . '/system/error/main.inc.php');
	/**
	 * @about - Check the variable types from a user-defined function's argument list.
	 */
	class hpl_func_arg {
		/** Get function parameter data.
		 * @access - private function
		 * @param - integer &$var (number id)
		 * @param - array &$itself (backtrace itself array)
		 * @param - array &$caller (backtrace caller array)
		 * @return - array(var=>value)|null
		 * @usage - self::parameter(&$var,&$itself,&$caller);
		 */
		private static function parameter(& $var, & $itself, & $caller) {
			if (isset ($caller['class']) && $caller['class'] == __CLASS__) {
				hpl_error :: cast($caller['class'] . '::' . $caller['function'] . '(): Called from the global scope - no function context', E_USER_WARNING, 2);
			}
			elseif (count($itself['args']) > 1) {
				hpl_error :: cast($itself['class'] . '::' . $itself['function'] . '(): Expects at most 1 parameters, ' . count($itself['args']) . ' given', E_USER_WARNING, 2);
			}
			elseif (!is_int($var)) {
				hpl_error :: cast($itself['class'] . '::' . $itself['function'] . '(): Expects parameter 1 to be integer, ' . strtolower(gettype($var)) . ' given', E_USER_WARNING, 2);
			}
			elseif (is_int($var) && $var < 0) {
				hpl_error :: cast($itself['class'] . '::' . $itself['function'] . '(): The argument number should be >= 0', E_USER_WARNING, 2);
			} else {
				//get parameters data list
				$parameters = & $caller['args'];
				if (isset ($caller['class'])) { //class function parameters
					$ref = new ReflectionMethod($caller['class'], $caller['function']);
					foreach ($ref->getParameters() as $key => $arg) {
						if ($key == $var) {
							if (!array_key_exists($key, $caller['args'])) {
								$parameters[$key] = ($arg->isDefaultValueAvailable() ? $arg->getDefaultValue() : NULL);
							}
							break;
						}
					}
				} else { //function parameters
					$ref = new ReflectionFunction($caller['function']);
					foreach ($ref->getParameters() as $key => $arg) {
						if ($key == $var) {
							if (!array_key_exists($key, $caller['args'])) {
								$parameters[$key] = ($arg->isDefaultValueAvailable() ? $arg->getDefaultValue() : NULL);
							}
							break;
						}
					}
				}
				//check parameter data exist
				if (array_key_exists($var, $parameters)) {
					return array (
						$parameters[$var]
					);
				} else {
					hpl_error :: cast($itself['class'] . '::' . $itself['function'] . '(): Argument ' . $var . ' not passed to function', E_USER_WARNING, 2);
				}
			}
			return;
		}
		/** Check the amount of defined arguments.
		 * @access - public function
		 * @return - boolean
		 * @usage - hpl_func_arg::delimit2error();
		 */
		public static function delimit2error() {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$caller = end($backtrace);
			if (isset ($caller['class']) && $caller['class'] == __CLASS__) {
				hpl_error :: cast($caller['class'] . '::' . $caller['function'] . '(): Called from the global scope - no function context', E_USER_WARNING, 1);
			}
			elseif (func_num_args() > 0) {
				hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 0 parameters, ' . func_num_args() . ' given', E_USER_WARNING, 1);
			}
			elseif (isset ($caller['class'])) { //class function parameters
				$ref = new ReflectionMethod($caller['class'], $caller['function']);
				if (count($caller['args']) > count($ref->getParameters())) {
					hpl_error :: cast($caller['class'] . '::' . $caller['function'] . '(): Expects at most ' . count($ref->getParameters()) . ' parameters, ' . count($caller['args']) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			} else { //function parameters
				$ref = new ReflectionFunction($caller['function']);
				if (count($caller['args']) > count($ref->getParameters())) {
					hpl_error :: cast($caller['function'] . '(): Expects at most ' . count($ref->getParameters()) . ' parameters, ' . count($caller['args']) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the array type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::array2error($var);
		 */
		public static function array2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_array($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be array, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the boolean type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::bool2error($var);
		 */
		public static function bool2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_bool($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be boolean, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the double type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::double2error($var);
		 */
		public static function double2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_float($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be double, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the integer type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::int2error($var);
		 */
		public static function int2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_int($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be integer, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the NULL type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::null2error($var);
		 */
		public static function null2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_null($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be NULL, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the numeric type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::numeric2error($var);
		 */
		public static function numeric2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_numeric($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be numeric, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the object type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::object2error($var);
		 */
		public static function object2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_object($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be object, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the resource type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::resource2error($var);
		 */
		public static function resource2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_resource($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be resource, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
		/** Check the string type.
		 * @access - public function
		 * @param - integer $var (number id)
		 * @return - boolean
		 * @usage - hpl_func_arg::string2error($var);
		 */
		public static function string2error($var = null) {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
			$itself = current($backtrace);
			$caller = end($backtrace);
			$parameter = self :: parameter($var, $itself, $caller);
			if (is_array($parameter)) {
				if (!is_string($parameter[0])) { //get parameter value
					hpl_error :: cast((isset ($caller['class']) ? $caller['class'] . '::' : '') . $caller['function'] . '(): Expects parameter ' . ($var +1) . ' to be string, ' . strtolower(gettype($parameter[0])) . ' given', E_USER_WARNING, 2);
				} else {
					return false;
				}
			}
			return true;
		}
	}
}
?>