<?php
if (!class_exists('hpl_csv')) {
	include (str_replace('\\', '/', dirname(__FILE__)) . '/system/file/main.inc.php');
	/**
	 * @about - comma separated values file processing.
	 * @param - string $fileLang (file language) : Default big5//ignore
	 * @param - string $sysLang (system language) : Default utf-8
	 * @return - object
	 * @usage - Object var name=new hpl_csv($fileLang,$sysLang);
	 */
	class hpl_csv {
		private $fpList, $lockList, $encodingForm, $sysLang, $fileLang;
		function __construct($fileLang = 'big5//ignore', $sysLang = 'utf-8') {
			$this->sysLang = $sysLang;
			$this->fileLang = $fileLang;
			$this->fpList = array ();
			$this->lockList = array ();
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: string2error(1)) {
				set_error_handler(__CLASS__ . '::ErrorHandler');
				$this->encodingForm = iconv($this->fileLang, $this->sysLang, '');
				$this->encodingForm = ($this->encodingForm === false ? false : true);
				restore_error_handler();
			}
		}
		/** Error handler.
		 * @access - private function
		 * @param - integer $errno (error number)
		 * @param - string $message (error message)
		 * @return - boolean|null
		 * @usage - set_error_handler(__CLASS__.'::ErrorHandler');
		 */
		private static function ErrorHandler($errno = null, $message = null) {
			if (!(error_reporting() & $errno)) {
				// This error code is not included in error_reporting
				return;
			}
			//replace message target function
			$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
			$caller = end($caller);
			$message = __CLASS__ . '::' . $caller['function'] . '(): ' . $message;
			//echo message
			hpl_error :: cast($message, $errno, 3);
			/* Don't execute PHP internal error handler */
			return true;
		}
		/** Opens localhost file.
		 * @access - public function
		 * @param - string $path (file path)
		 * @param - string $mode (mode to the stream r,w,a)
		 * @param - boolean $lock (lock file mode) : Default true
		 * @return - resource|boolean
		 * @usage - Object->open($path,$mode,$lock);
		 */
		public function open($path = null, $mode = null, $lock = true) {
			$fp = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: string2error(1) && !hpl_func_arg :: bool2error(2)) {
				if (strlen($path) > 0) {
					clearstatcache();
					$path = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($path) && hpl_path :: is_files($path) && (($mode == 'r' && is_file($path) && is_readable($path)) || ($mode != 'r' && hpl_file :: name($path) && hpl_file :: extension($path) == 'csv'))) {
						$mode = strtolower($mode);
						$modes = array ('r','w','a');
						if (!in_array($mode, $modes)) {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid mode specified', E_USER_WARNING, 1);
							return false;
						}
						elseif ($this->encodingForm) {
							if ($mode != 'r') { //check dir
								$dir = hpl_file :: directory($path);
								if (!file_exists($dir)) {
									set_error_handler(__CLASS__ . '::ErrorHandler');
									$result = mkdir($dir, 0755, true);
									restore_error_handler();
								}
								elseif (is_dir($dir)) {
									$result = true;
								} else {
									$result = false;
									hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $path, E_USER_WARNING, 1);
								}
							} else {
								$result = true;
							}
							if ($result) { //check dir result
								set_error_handler(__CLASS__ . '::ErrorHandler');
								$fp = fopen($path, $mode);
								if ($fp) {
									if ($mode == 'r') {
										$loadLock = ($lock ? flock($fp, LOCK_SH) : false);
									} else {
										$loadLock = ($lock ? flock($fp, LOCK_EX) : false);
									}
									if ($lock && !$loadLock) {
										fclose($fp);
										$fp = false;
										hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $path . '): failed to open stream: Advisory file locking failures', E_USER_NOTICE, 1);
									} else {
										$this->fpList[] = & $fp;
										$this->lockList[] = $loadLock;
									}
								}
								restore_error_handler();
							}
						} else {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $path . '): failed to open stream: Wrong charset encoding', E_USER_WARNING, 1);
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $path, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $fp;
		}
		/** Format line as CSV and write to file pointer.
		 * @access - public function
		 * @param - string &$handle (a valid file pointer)
		 * @param - array $data (once array data)
		 * @return - boolean
		 * @usage - Object->puts(&$handle,$data);
		 */
		public function puts(& $handle = null, $data = null) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: resource2error(0) && !hpl_func_arg :: array2error(1)) {
				if (!in_array($handle, $this->fpList, true)) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): ' . $handle . ' is not a valid stream resource', E_USER_WARNING, 1);
				} else {
					foreach ($data as $i => $val) {
						if (is_array($val)) {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Array to string conversion is incorrect', E_USER_WARNING, 1);
							return false;
						} else {
							$data[$i] = iconv($this->sysLang, $this->fileLang, $val);
						}
					}
					set_error_handler(__CLASS__ . '::ErrorHandler');
					$result = (fputcsv($handle, $data) > 0 ? true : false);
					restore_error_handler();
				}
			}
			return $result;
		}
		/** Gets line from file pointer and parse for CSV fields.
		 * @access - public function
		 * @param - string &$handle (a valid file pointer)
		 * @param - string $length (must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). It became optional in PHP 5. Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower) : Default 0
		 * @param - string $delimiter (the optional delimiter parameter sets the field delimiter (one character only)) : Default ,
		 * @param - string $enclosure (the optional enclosure parameter sets the field enclosure character (one character only)) : Default "
		 * @return - array|boolean
		 * @usage - Object->gets(&$handle,$length,$delimiter,$enclosure,$escape);
		 */
		public function gets(& $handle = null, $length = 0, $delimiter = ',', $enclosure = '"') {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: resource2error(0) && !hpl_func_arg :: int2error(1) && !hpl_func_arg :: string2error(2) && !hpl_func_arg :: string2error(3)) {
				if (!in_array($handle, $this->fpList, true)) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): ' . $handle . ' is not a valid stream resource', E_USER_WARNING, 1);
				}
				elseif ($length < 0) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Length parameter must be >= 0', E_USER_WARNING, 1);
				}
				elseif (!$delimiter) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): missing terminating delimiter character', E_USER_WARNING, 1);
				}
				elseif (!$enclosure) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): missing parenthesis character', E_USER_WARNING, 1);
				} else {
					if (!feof($handle)) {
						set_error_handler(__CLASS__ . '::ErrorHandler');
						//fgetcsv data
						$d = preg_quote($delimiter);
						$e = preg_quote($enclosure);
						$_line = '';
						$eof = false;
						while ($eof != true) {
							$_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
							$itemcnt = preg_match_all('/' . $e . '/', $_line);
							if ($itemcnt % 2 == 0) {
								$eof = true;
							}
						}
						$_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
						$_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
						preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
						$_csv_data = $_csv_matches[1];
						for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
							$_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $_csv_data[$_csv_i]);
							$_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
						}
						$data = (empty ($_line) ? false : $_csv_data);
						restore_error_handler();
						//fgetcsv data
						if (is_array($data)) {
							foreach ($data as $key => $val) {
								$data[$key] = iconv($this->fileLang, $this->sysLang, $val);
							}
							return $data;
						}
					}
				}
			}
			return false;
		}
		/** Closes an open file pointer.
		 * @access - public function
		 * @param - string &$handle (a valid file pointer)
		 * @return - boolean
		 * @usage - Object->close(&$handle);
		 */
		public function close(& $handle = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: resource2error(0)) {
				if (!in_array($handle, $this->fpList, true)) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): ' . $handle . ' is not a valid stream resource', E_USER_WARNING, 1);
				} else {
					$sort = array_search($handle, $this->fpList, true);
					if ($this->lockList[$sort]) {
						flock($handle, LOCK_UN);
					}
					fclose($handle);
					unset ($this->lockList[$sort]);
					$this->lockList = array_values($this->lockList);
					unset ($this->fpList[$sort]);
					$this->fpList = array_values($this->fpList);
					return true;
				}
			}
			return false;
		}
		function __destruct() {
			foreach ($this->fpList as $sort => $resource) {
				if (is_resource($resource)) {
					if ($this->lockList[$sort]) {
						flock($resource, LOCK_UN);
					}
					fclose($resource);
				}
			}
		}
	}
}
?>