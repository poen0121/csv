<?php
if (!class_exists('hpl_csv')) {
	include (strtr(dirname(__FILE__), '\\', '/') . '/system/file/main.inc.php');
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
				$this->encodingForm = iconv($this->fileLang, $this->sysLang, '');
				$this->encodingForm = ($this->encodingForm === false ? false : true);
			}
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
				if (isset ($path { 0 })) {
					clearstatcache();
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && hpl_path :: is_files($normPath) && (($mode == 'r' && is_file($normPath) && is_readable($normPath)) || ($mode != 'r' && hpl_file :: name($normPath) && hpl_file :: extension($normPath) == 'csv'))) {
						$mode = strtolower($mode);
						$modes = array ('r', 'w', 'a');
						if (!in_array($mode, $modes)) {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid mode specified', E_USER_WARNING, 1);
							return false;
						}
						elseif ($this->encodingForm) {
							if ($mode != 'r') { //check dir
								$dir = hpl_file :: directory($normPath);
								if (!file_exists($dir)) {
									$result = mkdir($dir, 0755, true);
								}
								elseif (is_dir($dir)) {
									$result = true;
								} else {
									$result = false;
									hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
								}
							} else {
								$result = true;
							}
							if ($result) { //check dir result
								$fp = fopen($normPath, $mode);
								if ($fp) {
									if ($mode == 'r') {
										$loadLock = ($lock ? flock($fp, LOCK_SH) : false);
									} else {
										$loadLock = ($lock ? flock($fp, LOCK_EX) : false);
									}
									if ($lock && !$loadLock) {
										fclose($fp);
										$fp = false;
										hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $normPath . '): failed to open stream: Advisory file locking failures', E_USER_NOTICE, 1);
									} else {
										$this->fpList[] = $fp;
										$this->lockList[] = $loadLock;
									}
								}
							}
						} else {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $normPath . '): failed to open stream: Wrong charset encoding', E_USER_WARNING, 1);
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
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
					$result = (fputcsv($handle, $data) > 0 ? true : false);
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
						//fgetcsv data
						$d = preg_quote($delimiter);
						$e = preg_quote($enclosure);
						$line = '';
						$eof = false;
						while ($eof != true) {
							$line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
							$itemcnt = preg_match_all('/' . $e . '/', $line);
							if ($itemcnt % 2 == 0) {
								$eof = true;
							}
						}
						$csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($line));
						$csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
						$csv_matches = null;
						preg_match_all($csv_pattern, $csv_line, $csv_matches);
						$csv_data = $csv_matches[1];
						for ($csv_i = 0; $csv_i < count($csv_data); $csv_i++) {
							$csv_data[$csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $csv_data[$csv_i]);
							$csv_data[$csv_i] = str_replace($e . $e, $e, $csv_data[$csv_i]);
						}
						$data = (empty ($line) ? false : $csv_data);
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