<?php
if (!class_exists('hpl_error')) {
	/**
	 * @about - Throw an error by error_reporting control, and save the log records.
	 */
	class hpl_error {
		/** Throws an error and saves the error log.
		 * @access - public function
		 * @param - string $errorMessage (error message)
		 * @param - integer $errno (error level by error_reporting) : Default E_USER_NOTICE
		 * @param - integer $echoDepth (location echo depth) : Default 0
		 * @param - string $logTitle (log title) : Default 'PHP' is system reserved words
		 * @return - boolean
		 * @usage - hpl_error::cast($errorMessage,$errno,$echoDepth,$logTitle);
		 */
		public static function cast($errorMessage = null, $errno = E_USER_NOTICE, $echoDepth = 0, $logTitle = 'PHP') {
			$numargs = func_num_args();
			if ($numargs > 4) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 4 parameters, ' . func_num_args() . ' given', E_USER_WARNING, 1);
				}
			}
			elseif (!is_string($errorMessage)) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be string, ' . strtolower(gettype($errorMessage)) . ' given', E_USER_WARNING, 1);
				}
			}
			elseif (!is_int($errno)) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 2 to be integer, ' . strtolower(gettype($errno)) . ' given', E_USER_WARNING, 1);
				}
			}
			elseif (!is_int($echoDepth)) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 3 to be integer, ' . strtolower(gettype($echoDepth)) . ' given', E_USER_WARNING, 1);
				}
			}
			elseif (is_int($echoDepth) && $echoDepth < 0) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): The echo depth should be >= 0', E_USER_WARNING, 1);
				}
			}
			elseif (!is_string($logTitle)) {
				if (error_reporting() & E_USER_WARNING) {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 4 to be string, ' . strtolower(gettype($logTitle)) . ' given', E_USER_WARNING, 1);
				}
			} else { //call error
				$title = '';
				switch ($errno) {
					case E_PARSE :
					case E_ERROR :
					case E_CORE_ERROR :
					case E_COMPILE_ERROR :
					case E_USER_ERROR :
						$title = 'Fatal error';
						break;
					case E_WARNING :
					case E_USER_WARNING :
					case E_COMPILE_WARNING :
					case E_RECOVERABLE_ERROR :
						$title = 'Warning';
						break;
					case E_NOTICE :
					case E_USER_NOTICE :
						$title = 'Notice';
						break;
					case E_STRICT :
						$title = 'Strict';
						break;
					case E_DEPRECATED :
					case E_USER_DEPRECATED :
						$title = 'Deprecated';
						break;
					default :
						$title = 'Error [' . $errno . ']';
						break;
				}
				//This error code is included in error_reporting
				if ((error_reporting() & $errno)) {
					$message = '<br /><b>' . $title . '</b>: ' . trim($errorMessage) . self :: where($echoDepth);
					if (preg_match('/^(on|(\+|-)?[0-9]*[1-9]+[0-9]*)$/i', ini_get('log_errors'))) {
						$logTitle = strtoupper(trim($logTitle));
						if (isset ($_SERVER['PEEL_OFF_ERROR_LOG_FILE']) && isset ($_SERVER['PEEL_OFF_NAME'])) {
							$file = (isset ($_SERVER['PEEL_OFF_ERROR_LOG_FILE']) ? str_replace('\\', '/', $_SERVER['PEEL_OFF_ERROR_LOG_FILE']) : '');
							$peelName = (isset ($_SERVER['PEEL_OFF_NAME']) ? strtoupper(trim($_SERVER['PEEL_OFF_NAME'])) : '');
							if (($numargs < 4 || ($numargs == 4 && $logTitle != 'PHP')) && $peelName != 'PHP' && strlen($file) > 0 && !filter_var($file, FILTER_VALIDATE_URL) && substr($file, -1, 1) !== '/') {
								error_log(date('[d-M-Y H:i:s e] ') . ($numargs == 4 ? $logTitle : $peelName) . ' ' . strip_tags($message) . PHP_EOL, 3, $file);
							} else {
								error_log($logTitle . ' ' . strip_tags($message), 0);
							}
						} else {
							error_log($logTitle . ' ' . strip_tags($message), 0);
						}
					}
					if (preg_match('/^(on|(\+|-)?[0-9]*[1-9]+[0-9]*)$/i', ini_get('display_errors'))) {
						echo PHP_EOL . (isset ($_SERVER['argc']) && $_SERVER['argc'] >= 1 ? strip_tags($message) : $message) . PHP_EOL;
					}
				}
				if ($title == 'Fatal error') {
					exit;
				}
				return true;
			}
			return false;
		}
		/** Get file and line echo location.
		 * @access - private function
		 * @param - integer &$echoDepth (location echo depth)
		 * @return - string
		 * @usage - self::where(&$echoDepth);
		 */
		private static function where(& $echoDepth) {
			$trace = (isset ($_SERVER['ERROR_STACK_TRACE']) ? preg_match('/^(on|(\+|-)?[0-9]*[1-9]+[0-9]*)$/i', $_SERVER['ERROR_STACK_TRACE']) : false);
			$baseDepth = 2 + $echoDepth;
			$caller = debug_backtrace(($trace ? DEBUG_BACKTRACE_PROVIDE_OBJECT : DEBUG_BACKTRACE_IGNORE_ARGS), ($trace ? 0 : $baseDepth));
			$rows = count($caller);
			$main = ($rows < $baseDepth ? ($rows -1) : ($baseDepth -1)); //main caller location
			$message = ' in <b>' . $caller[$main]['file'] . '</b> on line <b>' . $caller[$main]['line'] . '</b><br />';
			if ($trace && $rows >= $baseDepth) {
				$baseDepth = ($baseDepth > 2 ? $baseDepth -1 : 2);
				for ($i = $baseDepth; $i < $rows; $i++) {
					if ($i == $baseDepth) {
						$message .= PHP_EOL . 'Stack trace:' . PHP_EOL . '<br />';
					}
					$argsList = ''; //args info
					if (isset ($caller[$i]['args'])) {
						foreach ($caller[$i]['args'] as $sort => $args) {
							$argsList .= ($sort > 0 ? ', ' : '');
							switch (gettype($args)) {
								case 'string' :
									$argsList .= '\'' . (mb_strlen($args, 'utf-8') > 20 ? mb_substr($args, 0, 17, 'utf-8') . '...' : $args) . '\'';
									break;
								case 'array' :
									$argsList .= 'Array';
									break;
								case 'object' :
									$argsList .= get_class($args) . ' Object';
									break;
								case 'resource' :
									$argsList .= get_resource_type($args) . ' Resource';
									break;
								case 'boolean' :
									$argsList .= ($args ? 'true' : 'false');
									break;
								case 'NULL' :
									$argsList .= 'NULL';
									break;
								default :
									$argsList .= $args;
									break;
							}
						}
					}
					$message .= '#' . ($i - $baseDepth) . ' ' . $caller[$i]['file'] . '(' . $caller[$i]['line'] . '):' . (isset ($caller[$i]['class']) ? ' ' . $caller[$i]['class'] . $caller[$i]['type'] : ' ') . $caller[$i]['function'] . '(' . $argsList . ')' . ($i < ($rows -1) ? PHP_EOL : '') . '<br />';
				}
			}
			return $message;
		}
	}
}
?>