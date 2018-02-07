<?php
if (!class_exists('hpl_file')) {
	include (strtr(dirname(__FILE__), '\\', '/') . '/system/path/main.inc.php');
	/**
	 * @about - file operations.
	 */
	class hpl_file {
		/** Get localhost file permissions code.
		 * @access - public function
		 * @param - string $path (file path)
		 * @return - string|boolean
		 * @usage - hpl_file::get_mod($path);
		 */
		public static function get_mod($path = null) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (isset ($path { 0 })) {
					clearstatcache();
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && (is_file($normPath) || is_dir($normPath))) {
						$result = substr(sprintf('%o', fileperms($normPath)), -4);
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $result;
		}
		/** Set localhost file on permissions.
		 * @access - public function
		 * @param - string $path (file path)
		 * @param - integer $power (file permissions number three octal)
		 * @return - boolean
		 * @usage - hpl_file::ch_mod($path,$power);
		 */
		public static function ch_mod($path = null, $power = null) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: int2error(1)) {
				if (isset ($path { 0 })) {
					clearstatcache();
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && (is_file($normPath) || is_dir($normPath))) {
						$result = chmod($normPath, $power);
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $result;
		}
		/** Get the script file memory currently in use.
		 * @note - Set type mode to TRUE to get the real size of memory allocated from system.
		 * @note - If not set or FALSE only the memory used by emalloc() is reported.
		 * @access - public function
		 * @param - boolean $type (type mode) : Default false
		 * @return - string|boolean
		 * @usage - hpl_file::memory($type);
		 */
		public static function memory($type = false) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: bool2error(0)) {
				if (function_exists('memory_get_usage')) {
					return self :: size2unit((double) memory_get_usage($type));
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Call to undefined memory_get_usage()', E_USER_ERROR, 1);
				}
			}
			return false;
		}
		/** Get path's file full name.
		 * @access - public function
		 * @param - string $path (path)
		 * @param - string $query_keep (after the question mark ? data keep mode) : Default false
		 * @return - string|boolean
		 * @usage - hpl_file::fullname($path,$queryKeep);
		 */
		public static function fullname($path = null, $queryKeep = false) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: bool2error(1)) {
				$basePath = pathInfo(hpl_path :: norm($path), PATHINFO_BASENAME);
				return ($queryKeep ? $basePath : str_replace(strstr($basePath, '?'), '', $basePath));
			}
			return false;
		}
		/** Get path's file name.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_file::name($path);
		 */
		public static function name($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				return pathInfo(hpl_path :: norm($path), PATHINFO_FILENAME);
			}
			return false;
		}
		/** Get path's file extension.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_file::extension($path);
		 */
		public static function extension($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				return strtolower(pathInfo(hpl_path :: norm($path), PATHINFO_EXTENSION));
			}
			return false;
		}
		/** Get path's file directory.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_file::directory($path);
		 */
		public static function directory($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				$dirPath = pathInfo($path, PATHINFO_DIRNAME);
				return (isset ($dirPath { 0 }) ? (($dirPath == '\\' ? '.' : hpl_path :: norm($dirPath)) . '/') : '');
			}
			return false;
		}
		/** Get unit format size estimate data size.
		 * @access - public function
		 * @param - string $unitSize (bytes unit size)
		 * @return - double|boolean
		 * @usage - hpl_file::unit2size($unitSize);
		 */
		public static function unit2size($unitSize = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (preg_match('/^([0-9]|[1-9]{1}[0-9]{0,2}|[1-9]{1}[0-9]{0,2}([,]{1}[0-9]{3})+){1}(\.[0-9]{1,2}){0,1}[ \f\r\t\n]{1}(BYTE|KB|MB|GB|TB|PB|EB|ZB|YB)$/i', $unitSize)) {
					$data = explode(' ', $unitSize);
					$size = (double) str_replace(',', '', $data[0]);
					$type = strtoupper($data[1]);
					$unit = array ('BYTE' => 0,'KB' => 1,'MB' => 2,'GB' => 3,'TB' => 4,'PB' => 5,'EB' => 6,'ZB' => 7,'YB' => 8);
					$size = floor($size * pow(1024, $unit[$type]));
					if ($size > (1024 * pow(1024, 8))) {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Size has been unable to handle', E_USER_WARNING, 1);
					} else {
						return $size;
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Unrecognized units format', E_USER_WARNING, 1);
				}
			}
			return false;
		}
		/** Get data size estimate unit format size.
		 * @access - public function
		 * @param - double $size (bytes size number)
		 * @return - string|boolean
		 * @usage - hpl_file::size2unit($size);
		 */
		public static function size2unit($size = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: double2error(0)) {
				if ($size >= 0) {
					if ($size > (1024 * pow(1024, 8))) {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Size has been unable to handle', E_USER_WARNING, 1);
					} else {
						$unit = array ('Byte','KB','MB','GB','TB','PB','EB','ZB','YB');
						$flag = 0;
						$bytes = $size;
						while ($flag < 8 && $bytes >= 1024) {
							$bytes = $bytes / 1024;
							$flag++;
						}
						$result = round($bytes, 2);
						$result = ($bytes > $result ? $result +0.01 : $result);
						return ($flag > 0 ? number_format($result, 2) : number_format($result)) . ' ' . $unit[$flag];
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): The number of bytes to be >= 0', E_USER_WARNING, 1);
				}
			}
			return false;
		}
		/** Get data size unit format estimate min unit size.
		 * @access - public function
		 * @param - string $unitSize (bytes unit size)
		 * @return - string|boolean
		 * @usage - hpl_file::unit2min($unitSize);
		 */
		public static function unit2min($unitSize = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (preg_match('/^([0-9]|[1-9]{1}[0-9]{0,2}|[1-9]{1}[0-9]{0,2}([,]{1}[0-9]{3})+){1}(\.[0-9]{1,2}){0,1}[ \f\r\t\n]{1}(BYTE|KB|MB|GB|TB|PB|EB|ZB|YB)$/i', $unitSize)) {
					$data = explode(' ', $unitSize);
					$size = (double) str_replace(',', '', $data[0]);
					$type = strtoupper($data[1]);
					$unit = array ('BYTE' => 0,'KB' => 1,'MB' => 2,'GB' => 3,'TB' => 4,'PB' => 5,'EB' => 6,'ZB' => 7,'YB' => 8);
					$size = floor($size * pow(1024, $unit[$type]));
					if ($size > (1024 * pow(1024, 8))) {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Size has been unable to handle', E_USER_WARNING, 1);
					} else {
						return number_format($size) . ' Byte';
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Unrecognized units format', E_USER_WARNING, 1);
				}
			}
			return false;
		}
		/** Get data size unit format estimate max unit size.
		 * @access - public function
		 * @param - string $unitSize (bytes unit size)
		 * @return - string|boolean
		 * @usage - hpl_file::unit2max($unitSize);
		 */
		public static function unit2max($unitSize = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (preg_match('/^([0-9]|[1-9]{1}[0-9]{0,2}|[1-9]{1}[0-9]{0,2}([,]{1}[0-9]{3})+){1}(\.[0-9]{1,2}){0,1}[ \f\r\t\n]{1}(BYTE|KB|MB|GB|TB|PB|EB|ZB|YB)$/i', $unitSize)) {
					$data = explode(' ', $unitSize);
					$size = (double) str_replace(',', '', $data[0]);
					$type = strtoupper($data[1]);
					$unit = array ('BYTE' => 0,'KB' => 1,'MB' => 2,'GB' => 3,'TB' => 4,'PB' => 5,'EB' => 6,'ZB' => 7,'YB' => 8);
					$size = floor($size * pow(1024, $unit[$type]));
					if ($size > (1024 * pow(1024, 8))) {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Size has been unable to handle', E_USER_WARNING, 1);
					} else {
						return self :: size2unit($size);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Unrecognized units format', E_USER_WARNING, 1);
				}
			}
			return false;
		}
		/** Get localhost file's estimate unit format size.
		 * @access - public function
		 * @param - string $path (file path)
		 * @return - string|boolean
		 * @usage - hpl_file::size($path);
		 */
		public static function size($path = null) {
			$sizeUnit = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (isset ($path { 0 })) {
					clearstatcache();
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && is_file($normPath)) {
						$size = filesize($normPath);
						if ($size !== false) {
							if ($size > (1024 * pow(1024, 8))) {
								hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Size has been unable to handle', E_USER_WARNING, 1);
							} else {
								$sizeUnit = self :: size2unit((double) $size);
							}
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $sizeUnit;
		}
		/** Load localhost file contents.
		 * @access - public function
		 * @param - string $path (file path)
		 * @param - string $mode (mode to the stream r,rb) : Default r
		 * @note - $mode :
		 * 		  'r'  >> Open for reading only; place the file pointer at the beginning of the file.
		 * 		  'rb' >> Open for reading only; place the bit file pointer at the beginning of the file.
		 * @param - boolean $lock (lock file mode) : Default true
		 * @return - string|boolean
		 * @usage - hpl_file::load($path,$mode,$lock);
		 */
		public static function load($path = null, $mode = 'r', $lock = true) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: string2error(1) && !hpl_func_arg :: bool2error(2)) {
				if (isset ($path { 0 })) {
					clearstatcache();
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && is_file($normPath) && is_readable($normPath)) {
						$useMode = strtolower($mode);
						$modes = array ('r','rb');
						if (!in_array($useMode, $modes)) {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid mode specified', E_USER_WARNING, 1);
							return false;
						}
						$fp = fopen($normPath, $useMode);
						if ($fp) {
							$action = ($lock ? flock($fp, LOCK_SH) : true);
							if ($action) {
								$bytes = filesize($normPath);
								if ($bytes !== false) {
									$result = stream_get_contents($fp, -1);
									$result = ($result !== false && !isset($result { 0 }) && $bytes > 0 ? false : $result);
									if ($result === false) {
										hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Unable to read file ' . $normPath, E_USER_NOTICE, 1);
									}
								}
								if ($lock) {
									flock($fp, LOCK_UN);
								}
							} else {
								hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $normPath . '): failed to open stream: Advisory file locking failures', E_USER_NOTICE, 1);
							}
							fclose($fp);
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $result;
		}
		/** Save the file contents to the localhost.
		 * @access - public function
		 * @param - string $path (file path)
		 * @param - string $content (content)
		 * @param - string $mode (mode to the stream w,a,wb,ab) : Default w
		 * @param - boolean $lock (lock file mode) : Default true
		 * @return - boolean
		 * @usage - hpl_file::save($path,$content,$mode,$lock);
		 */
		public static function save($path = null, $content = null, $mode = 'w', $lock = true) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: string2error(1) && !hpl_func_arg :: string2error(2) && !hpl_func_arg :: bool2error(3)) {
				if (isset ($path { 0 })) {
					$normPath = hpl_path :: norm($path);
					if (!hpl_path :: is_absolute($normPath) && hpl_path :: is_files($normPath) && self :: fullname($normPath)) {
						$useMode = strtolower($mode);
						$modes = array ('w','a','wb','ab');
						if (!in_array($useMode, $modes)) {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid mode specified', E_USER_WARNING, 1);
							return false;
						}
						//check dir
						clearstatcache();
						$dir = self :: directory($normPath);
						if (!file_exists($dir)) {
							$result = mkdir($dir, 0755, true);
						}
						elseif (is_dir($dir)) {
							$result = true;
						} else {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
						}
						if ($result) { //check dir result
							$result = false; //init result
							$bytes = strlen($content);
							$fp = fopen($normPath, $useMode);
							if ($fp) {
								$action = ($lock ? flock($fp, LOCK_EX) : true);
								if ($action) {
									$writeBytes = fwrite($fp, $content, $bytes);
									$result = ($writeBytes === $bytes ? true : false);
									if ($lock) {
										flock($fp, LOCK_UN);
									}
									if (!$result) {
										hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Unable to write to file ' . $normPath, E_USER_NOTICE, 1);
									}
								} else {
									hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(' . $normPath . '): failed to open stream: Advisory file locking failures', E_USER_NOTICE, 1);
								}
								fclose($fp);
							}
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normPath, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $result;
		}
		/** Create a directory to the localhost , if the directory exists will try to modify the permissions.
		 * @access - public function
		 * @param - string $dir (directory path)
		 * @param - integer $power (directory permissions number three octal) : Default 0755
		 * @return - boolean
		 * @usage - hpl_file::mk_dir($dir,$power);
		 */
		public static function mk_dir($dir = null, $power = 0755) {
			$result = false;
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: int2error(1)) {
				if ($dir) {
					$normDir = hpl_path :: norm($dir);
					if (!hpl_path :: is_absolute($normDir)) {
						clearstatcache();
						if (!file_exists($normDir)) {
							$result = mkdir($normDir, $power, true);
						}
						elseif (is_dir($normDir)) {
							$result = chmod($normDir, $power);
						} else {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normDir, E_USER_WARNING, 1);
						}
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Stat failed for ' . $normDir, E_USER_WARNING, 1);
					}
				} else {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Empty path supplied as input', E_USER_WARNING, 1);
				}
			}
			return $result;
		}
	}
}
?>