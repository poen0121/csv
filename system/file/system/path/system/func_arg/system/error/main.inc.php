<?php
if (!class_exists('hpl_error')) {
	/**
	 * @about - Use error_handler to capture error messages by using the error_reporting control to throw an error and save the log records.
	 */
	class hpl_error {
		private static $defaultLogTitle;
		private static $logTitle;
		private static $logFile;
		private static $castList;
		private static $trace;
		private static $errorHandler;
		private static $errorHandlerList;
		private static $exit;
		/** Error handler.
		 * @access - public function
		 * @return - boolean|null
		 * @usage - set_error_handler(__CLASS__.'::ErrorHandler');
		 */
		public static function ErrorHandler() {
			return hpl_error :: capture();
		}
		/** Set the user-defined error handler to start the function.
		 * @access - public function
		 * @param - callable $errorHandler (a callback or null default last declared value with the following signature)
		 * @return - boolean
		 * @usage - hpl_error::begin($errorHandler);
		 */
		public static function begin($errorHandler = null) {
			$numArgs = func_num_args();
			if ($numArgs > 1) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 1 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_callable($errorHandler) && !is_null($errorHandler)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be callable, ' . strtolower(gettype($errorHandler)) . ' given', E_USER_WARNING, 1);
			} else {
				if (!is_null($errorHandler)) {
					$errorHandler = (is_array($errorHandler) ? implode('::', $errorHandler) : $errorHandler);
					self :: $errorHandlerList[] = $errorHandler;
					self :: $errorHandler = $errorHandler;
				}
				/* set default error_handler */
				set_error_handler(self :: $errorHandler ? self :: $errorHandler : __CLASS__ . '::ErrorHandler');
				return true;
			}
			return false;
		}
		/** Set PHP log errors to specified default file.
		 * @access - public function
		 * @param - string $path (file path)
		 * @param - boolean $peel (allow peel off to capture the error pattern) : Default false
		 * @return - boolean
		 * @usage - hpl_error::error_log_file($path,$peel);
		 */
		public static function error_log_file($path = null, $peel = false) {
			$numArgs = func_num_args();
			if ($numArgs > 2) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 2 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_string($path)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be string, ' . strtolower(gettype($path)) . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_bool($peel)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 2 to be boolean, ' . strtolower(gettype($peel)) . ' given', E_USER_WARNING, 1);
			} else {
				$path = strtr($path, '\\', '/');
				if (isset ($path { 0 }) && !filter_var($path, FILTER_VALIDATE_URL) && substr($path, -1, 1) !== '/') {
					if (!$peel) {
						ini_set('error_log', $path);
						if (strtr(ini_get('error_log'), '\\', '/') === $path) {
							self :: $logFile = null;
							return true;
						}
					} else {
						ini_set('error_log', $path);
						self :: $logFile = $path;
						return true;
					}
				}
			}
			return false;
		}
		/** Set the error stack trace mode.
		 * @access - public function
		 * @param - boolean $switch (open or close the stack trace error mode)
		 * @return - boolean
		 * @usage - hpl_error::trace($switch);
		 */
		public static function trace($switch = null) {
			$numArgs = func_num_args();
			if ($numArgs > 1) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 1 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_bool($switch)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be boolean, ' . strtolower(gettype($switch)) . ' given', E_USER_WARNING, 1);
			} else {
				self :: $trace = $switch;
				return true;
			}
			return false;
		}
		/** Set the error cast function default log title.
		 * @access - public function
		 * @param - string $default (default error log title) : Default 'PHP' is system reserved words
		 * @return - boolean
		 * @usage - hpl_error::cast_log_title($default);
		 */
		public static function cast_log_title($default = null) {
			$numArgs = func_num_args();
			if ($numArgs > 1) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 1 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_string($default)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be string, ' . strtolower(gettype($default)) . ' given', E_USER_WARNING, 1);
			} else {
				self :: $defaultLogTitle = $default;
				return true;
			}
			return false;
		}
		/** Throws an error and sends a recessive signal message to the error_handler function.
		 * @note - The error_handler will catch the message 'ERROR_TOUCH_SIGNAL:' id string.
		 * @access - public function
		 * @param - string $message (the specified error message, the length limit of more than 1024 bytes, will be truncated)
		 * @param - integer $errno (error level by error_reporting) : Default E_USER_NOTICE
		 * @param - integer $echoDepth (location echo depth) : Default 0
		 * @param - string $logTitle (log title) : By default, the cast_log_title function default log title is used
		 * @return - boolean
		 * @usage - hpl_error::cast($message,$errno,$echoDepth,$logTitle);
		 */
		public static function cast($message = null, $errno = E_USER_NOTICE, $echoDepth = 0, $logTitle = '') {
			$numArgs = func_num_args();
			if ($numArgs > 4) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 4 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_string($message)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be string, ' . strtolower(gettype($message)) . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_int($errno)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 2 to be integer, ' . strtolower(gettype($errno)) . ' given', E_USER_WARNING, 1);
			}
			elseif (!is_int($echoDepth)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 3 to be integer, ' . strtolower(gettype($echoDepth)) . ' given', E_USER_WARNING, 1);
			}
			elseif (is_int($echoDepth) && $echoDepth < 0) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): The echo depth should be >= 0', E_USER_WARNING, 1);
			}
			elseif (!is_string($logTitle)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 4 to be string, ' . strtolower(gettype($logTitle)) . ' given', E_USER_WARNING, 1);
			} else {
				$echoDepth = $echoDepth +4;
				/* check function in error_handler */
				$inHandler = false;
				$errorHandler = false;
				$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 0);
				foreach ($caller as $i => $in) {
					$needle = (isset ($in['class']) ? $in['class'] . $in['type'] : '') . $in['function'];
					if (!$errorHandler && is_array(self :: $errorHandlerList) && in_array($needle, self :: $errorHandlerList)) {
						$errorHandler = $needle;
					}
					if (!isset ($in['file']) && !isset ($in['line'])) {
						$echoDepth = ($echoDepth >= (4 + $i) ? 4 + $i -1 : $echoDepth);
						$inHandler = true;
						break;
					}
				}
				/* build cast error info */
				self :: $castList[] = array (
					'echoDepth' => $echoDepth,
					'errno' => $errno,
					'message' => substr($message, 0, 1024), 
					'logTitle' => ($numArgs == 4 ? $logTitle : null), 
					'cancelTrace' => ($inHandler && $errorHandler === self :: $errorHandler)
					);
				end(self :: $castList);
				$id = key(self :: $castList);
				/* set default error_handler */
				$ord = set_error_handler(!self :: $errorHandler || ($inHandler && $errorHandler === self :: $errorHandler) ? __CLASS__ . '::ErrorHandler' : self :: $errorHandler);
				/* throws an error signal */
				trigger_error('ERROR_TOUCH_SIGNAL:' . $id);
				/* reset error_handler is null */
				if (!$ord) {
					restore_error_handler();
				}
				return true;
			}
			return false;
		}
		/** Capture error_handler information output error.
		 * @access - public function
		 * @param - boolean $exit (fatal error exit script) : Default true
		 * @return - boolean|null
		 * @usage - hpl_error::capture($exit);
		 */
		public static function capture($exit = true) {
			/* default native */
			$native = true;
			$echoDepth = 3;
			$trace = self :: $trace;
			$caller = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, ($trace ? 0 : 3));
			$rows = count($caller);
			$numArgs = func_num_args();
			if ($rows == 1) {
				/* limit call */
				self :: cast('Cannot instantiate function ' . __CLASS__ . '::' . __FUNCTION__ . '()', E_USER_ERROR, 1);
				return;
			}
			elseif ($numArgs > 1) {
				/* limit num args */
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects at most 1 parameters, ' . $numArgs . ' given', E_USER_WARNING, 1);
				return;
			}
			elseif (!is_bool($exit)) {
				self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Expects parameter 1 to be boolean, ' . strtolower(gettype($exit)) . ' given', E_USER_WARNING, 1);
				return;
			}
			/* gets error message */
			if (isset ($caller[1]['args'][1]) && is_string($caller[1]['args'][1]) && preg_match('/^ERROR_TOUCH_SIGNAL:[0-9]+$/', $caller[1]['args'][1])) {
				/* non-native error info */
				$native = false;
				$id = str_replace('ERROR_TOUCH_SIGNAL:', '', $caller[1]['args'][1]);
				$echoDepth = self :: $castList[$id]['echoDepth'];
				$errno = self :: $castList[$id]['errno'];
				$message = self :: $castList[$id]['message'];
				$logTitle = (isset (self :: $castList[$id]['logTitle']) ? self :: $castList[$id]['logTitle'] : (isset (self :: $defaultLogTitle) ? self :: $defaultLogTitle : 'PHP'));
				$trace = (self :: $castList[$id]['cancelTrace'] ? false : $trace);
				/* reeset caller info */
				if (!$trace) {
					$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $echoDepth);
					$rows = count($caller);
				}
				/* non-native location */
				$main = ($rows < $echoDepth ? ($rows -1) : ($echoDepth -1)); //main caller location
				$file = $caller[$main]['file'];
				$line = $caller[$main]['line'];
			} else {
				/* native error info */
				if (isset ($caller[1]['args'][0]) && is_int($caller[1]['args'][0]) && isset ($caller[1]['args'][1]) && is_string($caller[1]['args'][1]) && isset ($caller[1]['args'][2]) && is_string($caller[1]['args'][2]) && isset ($caller[1]['args'][3]) && is_int($caller[1]['args'][3])) {
					$errno = $caller[1]['args'][0];
					$message = $caller[1]['args'][1];
					$file = $caller[1]['args'][2];
					$line = $caller[1]['args'][3];
					$logTitle = 'PHP'; //native error title
				} else {
					self :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Exception capture', E_USER_ERROR, 1);
					return;
				}
			}
			/* output */
			if (!(error_reporting() & $errno) || self :: $exit) {
				/* this error code is not included in error_reporting */
				return;
			}
			/* build mark */
			$mark = '';
			switch ($errno) {
				case E_PARSE :
				case E_ERROR :
				case E_CORE_ERROR :
				case E_COMPILE_ERROR :
				case E_USER_ERROR :
					$mark = 'Fatal error';
					break;
				case E_WARNING :
				case E_USER_WARNING :
				case E_COMPILE_WARNING :
				case E_RECOVERABLE_ERROR :
					$mark = 'Warning';
					break;
				case E_NOTICE :
				case E_USER_NOTICE :
					$mark = 'Notice';
					break;
				case E_STRICT :
					$mark = 'Strict';
					break;
				case E_DEPRECATED :
				case E_USER_DEPRECATED :
					$mark = 'Deprecated';
					break;
				default :
					$mark = 'Error [' . $errno . ']';
					break;
			}
			/* build message */
			$is_record = preg_match('/^(on|(\+|-)?[0-9]*[1-9]+[0-9]*)$/i', ini_get('log_errors'));
			$is_display = preg_match('/^(on|(\+|-)?[0-9]*[1-9]+[0-9]*)$/i', ini_get('display_errors'));
			if ($is_record || $is_display) {
				$message = trim($message);
				$text = $mark . ': ' . $message;
				$text .= ' in ' . $file . ' on line ' . $line;
				$html = '<br /><b>' . $mark . '</b>: ' . htmlentities($message);
				$html .= ' in <b>' . $file . '</b> on line <b>' . $line . '</b><br />';
				/* stack trace */
				if ($trace && $rows >= $echoDepth) {
					$traceStart = $echoDepth -1;
					for ($i = $traceStart; $i < $rows; $i++) {
						if ($i == $traceStart) {
							$text .= PHP_EOL . 'Stack trace:' . PHP_EOL;
							$html .= PHP_EOL . 'Stack trace:' . PHP_EOL . '<br />';
						}
						$args2text = ''; //args to text
						$args2html = ''; //args to html
						if (isset ($caller[$i]['args'])) {
							foreach ($caller[$i]['args'] as $sort => $args) {
								$args2text .= ($sort > 0 ? ', ' : '');
								$args2html .= ($sort > 0 ? ', ' : '');
								switch (gettype($args)) {
									case 'string' :
										$argsString = '\'' . (mb_strlen($args, 'utf-8') > 20 ? mb_substr($args, 0, 17, 'utf-8') . '...' : $args) . '\'';
										$args2text .= $argsString;
										$args2html .= htmlentities($argsString);
										break;
									case 'array' :
										$args2text .= 'Array';
										$args2html .= 'Array';
										break;
									case 'object' :
										$args2text .= get_class($args) . ' Object';
										$args2html .= get_class($args) . ' Object';
										break;
									case 'resource' :
										$args2text .= get_resource_type($args) . ' Resource';
										$args2html .= get_resource_type($args) . ' Resource';
										break;
									case 'boolean' :
										$args2text .= ($args ? 'true' : 'false');
										$args2html .= ($args ? 'true' : 'false');
										break;
									case 'NULL' :
										$args2text .= 'NULL';
										$args2html .= 'NULL';
										break;
									default :
										$args2text .= $args;
										$args2html .= htmlentities($args);
										break;
								}
							}
						}
						/* trace location */
						$location = ($i - $traceStart) . ' ' . (isset ($caller[$i]['file']) && isset ($caller[$i]['line']) ? $caller[$i]['file'] . '(' . $caller[$i]['line'] . '): ' : '');
						/* trace message */
						$text .= '#' . $location . (isset ($caller[$i]['class']) ? $caller[$i]['class'] . $caller[$i]['type'] : '') . $caller[$i]['function'] . '(' . $args2text . ')' . ($i < ($rows -1) ? PHP_EOL : '');
						$html .= '#' . $location . (isset ($caller[$i]['class']) ? $caller[$i]['class'] . $caller[$i]['type'] : '') . $caller[$i]['function'] . '(' . $args2html . ')' . ($i < ($rows -1) ? PHP_EOL : '') . '<br />';
					}
				}
				/* output message */
				if ($is_record) {
					if (isset (self :: $logFile)) {
						error_log(date('[d-M-Y H:i:s e] ') . $logTitle . ' ' . $text . PHP_EOL, 3, self :: $logFile);
					} else {
						error_log($logTitle . ' ' . $text, 0);
					}
				}
				if ($is_display) {
					echo PHP_EOL, (isset ($_SERVER['argc']) && $_SERVER['argc'] >= 1 ? $text : $html), PHP_EOL;
				}
			}
			/* reset error_handler */
			restore_error_handler();
			/* remove */
			if (!$native) {
				self :: $castList[$id] = null;
				unset (self :: $castList[$id]);
			}
			/* fatal exit */
			if ($mark == 'Fatal error') {
				if ($exit) {
					exit;
				} else {
					self :: $exit = true; //force self exit
					error_reporting(0); //force PHP system
				}
			}
			return true;
		}
	}
}
?>