<?php
if (!class_exists('hpl_path')) {
	include (str_replace('\\', '/', dirname(__FILE__)) . '/system/func_arg/main.inc.php');
	/**
	 * @about - processing file path.
	 */
	class hpl_path {
		/** Change path to norm path.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_path::norm($path);
		 */
		public static function norm($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				return str_replace('\\', '/', $path);
			}
			return false;
		}
		/** Count actual arrive path's `../` relative layer number.
		 * @access - public function
		 * @param - string $path (relative path)
		 * @return - integer|boolean
		 * @usage - hpl_path::relative_layer_count($path);
		 */
		public static function relative_layer_count($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($path) || (!self :: is_root_model($path) && !self :: is_relative($path))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				} else {
					$path = self :: arrive($path);
					$path = explode('/', $path);
					$count = 0;
					foreach ($path as $part) {
						if (preg_match('/^\.\.$/', $part)) {
							$count += 1;
						} else {
							break;
						}
					}
					return $count;
				}
			}
			return false;
		}
		/** Get the host path's nexus relative full path based on the current script and document root target script.
		 * @access - public function
		 * @param - string $path (path)
		 * @param - string $scriptName (script name)
		 * @return - string|boolean
		 * @usage - hpl_path::nexus_full_relative($path,$scriptName);
		 */
		public static function nexus_full_relative($path = null, $scriptName = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: string2error(1)) {
				if (self :: is_absolute($path) || (!self :: is_root_model($path) && !self :: is_relative($path))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument by parameter 1', E_USER_WARNING, 1);
				}
				elseif (self :: is_absolute($scriptName) || (!self :: is_root_model($scriptName) && !self :: is_relative($scriptName))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument by parameter 2', E_USER_WARNING, 1);
				} else {
					$isRootModel = self :: is_root_model($path);
					$scriptName = self :: clean($scriptName);
					$scriptPathLen = self :: relative_layer_count(self :: relative($scriptName));
					$pathLen = self :: relative_layer_count(self :: full_relative($path));
					$ownPath = explode('/', $scriptName);
					$ownPath = array_slice($ownPath, 1, -1);
					$layer = $pathLen - $scriptPathLen; //more upper layers
					$path = self :: arrive($path);
					return ($layer > 0 ? str_repeat('../', $layer) : '') . (count($ownPath) > 0 ? str_repeat('../', count($ownPath)) : ($layer > 0 ? '' : './')) . substr((!$isRootModel ? self :: script($path) : self :: clean($path)), 1);
				}
			}
			return false;
		}
		/** Get the host path's relative full path based on the current script.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_path::full_relative($path);
		 */
		public static function full_relative($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($path) || (!self :: is_root_model($path) && !self :: is_relative($path))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				} else {
					$scriptDir = getcwd();
					if ($scriptDir !== false) {
						$scriptDir = self :: norm($scriptDir);
						$scriptDir = (substr($scriptDir, -1, 1) !== '/' ? $scriptDir . '/' : $scriptDir);
						$scriptDir = self :: clean($scriptDir);
						$ownPath = explode('/', $scriptDir);
						$ownPath = array_slice($ownPath, 1, -1);
						$isRootModel = self :: is_root_model($path);
						$path = self :: arrive($path);
						$layer = self :: relative_layer_count($path) - count($ownPath);
						$layer = ($isRootModel ? count($ownPath) + $layer : $layer); //more upper layers
						return ($layer > 0 ? str_repeat('../', $layer) : '') . (count($ownPath) > 0 ? str_repeat('../', count($ownPath)) : ($layer > 0 ? '' : './')) . substr((!$isRootModel ? self :: script($path) : self :: clean($path)), 1);
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Parent directories does not have the readable', E_USER_WARNING, 1);
					}
				}
			}
			return false;
		}
		/** Get the host path's script name based on the current script and document root.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - string|boolean
		 * @usage - hpl_path::script($path);
		 */
		public static function script($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($path) || (!self :: is_root_model($path) && !self :: is_relative($path))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				} else {
					if (self :: is_root_model($path)) {
						return self :: clean(self :: arrive($path));
					} else {
						$scriptDir = getcwd();
						if ($scriptDir !== false) {
							$scriptDir = self :: norm($scriptDir);
							$scriptDir = (substr($scriptDir, -1, 1) !== '/' ? $scriptDir . '/' : $scriptDir);
							$scriptDir = self :: clean($scriptDir);
							$ownPath = explode('/', $scriptDir);
							$ownPathLen = count($ownPath) - 1;
							$path = self :: arrive($path);
							$cut = self :: relative_layer_count($path); //for cut own path
							$path = self :: clean($path);
							$suffixPath = explode('/', $path);
							$suffixPath = array_slice($suffixPath, 1);
							$path = implode('/', array_merge(array_slice($ownPath, 1, - ($cut +1)), $suffixPath));
							return (substr($path, 0, 1) !== '/' ? '/' . $path : $path);
						} else {
							hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Parent directories does not have the readable', E_USER_WARNING, 1);
						}
					}
				}
			}
			return false;
		}
		/** Get the target script's relative path based on the current script and document root.
		 * @access - public function
		 * @param - string $scriptName (script name)
		 * @return - string|boolean
		 * @usage - hpl_path::relative($scriptName);
		 */
		public static function relative($scriptName = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($scriptName) || (!self :: is_root_model($scriptName) && !self :: is_relative($scriptName))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				} else {
					$scriptDir = getcwd();
					if ($scriptDir !== false) {
						$scriptDir = self :: norm($scriptDir);
						$scriptDir = (substr($scriptDir, -1, 1) !== '/' ? $scriptDir . '/' : $scriptDir);
						$scriptDir = self :: clean($scriptDir);
						$ownPath = explode('/', $scriptDir);
						$ownPath = array_slice($ownPath, 1, -1);
						return (count($ownPath) > 0 ? str_repeat('../', count($ownPath)) : './') . substr(self :: clean($scriptName), 1);
					} else {
						hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Parent directories does not have the readable', E_USER_WARNING, 1);
					}
				}
			}
			return false;
		}
		/** Get the target script's absolute path based on the document root.
		 * @access - public function
		 * @param - string $scriptName (script name)
		 * @return - string|boolean
		 * @usage - hpl_path::absolute($scriptName);
		 */
		public static function absolute($scriptName = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($scriptName) || (!self :: is_root_model($scriptName) && !self :: is_relative($scriptName))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				}
				elseif (isset ($_SERVER['REQUEST_SCHEME'], $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']) && strlen($_SERVER['REQUEST_SCHEME']) > 0 && strlen($_SERVER['SERVER_NAME']) > 0 && strlen($_SERVER['SERVER_PORT']) > 0) {
					return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443' ? ':' . $_SERVER['SERVER_PORT'] : '') . self :: clean($scriptName);
				}
			}
			return false;
		}
		/** Returns a parent directory's script name based on the document root.
		 * @access - public function
		 * @param - string $scriptName (script name)
		 * @return - string|boolean
		 * @usage - hpl_path::cutdir($scriptName);
		 */
		public static function cutdir($scriptName = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($scriptName) || (!self :: is_root_model($scriptName) && !self :: is_relative($scriptName))) {
					hpl_error :: cast(__CLASS__ . '::' . __FUNCTION__ . '(): Invalid argument', E_USER_WARNING, 1);
				} else {
					$path = self :: clean($scriptName);
					$path = self :: norm(dirname($path));
					return $path . (substr($path, -1, 1) !== '/' ? '/' : '');
				}
			}
			return false;
		}
		/** Arrive path part.
		 * @access - private function
		 * @param - string $path (path)
		 * @param - boolean $mode (keep empty string directory)
		 * @return - string
		 * @usage - self::arrive_part($path);
		 */
		private static function arrive_part($path = null, $mode = null) {
			if (strlen($path) > 0) {
				$path = explode('/', $path);
				$path = array_reverse($path);
				$end = (count($path) - 1);
				$layer = 0;
				$keepPath = array ();
				foreach ($path as $sort => $part) {
					if (preg_match('/^\.\.$/', $part)) {
						if ($sort == 0) {
							$keepPath[] = '';
						}
						$layer++;
					} else {
						if (preg_match('/^\.$/', $part)) {
							if ($sort == 0) {
								$keepPath[] = '';
							}
						} else {
							if ($layer == 0) {
								if (strlen($part) > 0 || (strlen($part) == 0 && $sort < $end)) {
									$keepPath[] = $part;
								}
							} else {
								if ($sort < $end || (strlen($part) > 0 && $sort == $end)) {
									$layer--;
								}
							}
						}
					}
				}
				$keepPath = array_reverse($keepPath);
				$path = ($layer > 0 ? str_repeat('../', $layer) : './') . implode('/', $keepPath);
				return ($mode ? $path : preg_replace('/(\/)+/', '/', $path));
			} else {
				return './';
			}

		}
		/** Returns the full path to arrive.
		 * @access - public function
		 * @param - string $path (path)
		 * @param - boolean $mode (keep empty string directory) : Default false
		 * @return - string|boolean
		 * @usage - hpl_path::arrive($path);
		 */
		public static function arrive($path = null, $mode = false) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				$path = self :: norm($path);
				if (self :: is_absolute($path)) {
					$uriPath = parse_url($path, PHP_URL_PATH);
					if ($uriPath) {
						if ($uriPath == '//') {
							$schemeLen = strpos($path, '://', 0) + 3;
							$divide = $schemeLen +strpos(substr($path, $schemeLen), '//', 0);
						} else {
							$divide = strpos($path, $uriPath, 0);
						}
						return substr($path, 0, $divide) . self :: clean(self :: arrive_part($uriPath, $mode)) . substr($path, $divide +strlen($uriPath));
					} else {
						return $path;
					}
				}
				elseif (self :: is_root_model($path)) {
					$path = substr($path, strlen(self :: document_root()));
				}
				elseif (!self :: is_relative($path)) {
					$uriScheme = strstr($path, ':', -1);
					$path = substr($path, strlen($uriScheme) + 1);
					return $uriScheme . ':' . self :: clean(self :: arrive_part($path, $mode));
				}
				return self :: arrive_part($path, $mode);
			}
			return false;
		}
		/** Clean path part.
		 * @access - private function
		 * @param - string $path (path)
		 * @param - boolean $mode (keep empty string directory)
		 * @return - string
		 * @usage - self::clean_part($path);
		 */
		private static function clean_part($path = null, $mode = null) {
			if (strlen($path) > 0) {
				$pathPart = explode('/', $path);
				$end = (count($pathPart) - 1);
				$path = '';
				foreach ($pathPart as $sort => $part) {
					if (preg_match('/^(\.\.|\.)$/', $part)) {
						$path .= ($sort == 0 ? '/' : '');
					} else {
						if (strlen($part) == 0) {
							$path .= (!$mode || $sort == $end ? '' : '/');
						} else {
							$path .= $part . ($sort < $end ? '/' : '');
						}
					}
				}
			}
			return (strlen($path) > 0 ? (substr($path, 0, 1) !== '/' ? '/' : '') . $path : '/');
		}
		/** Get the correct full path script name.
		 * @access - public function
		 * @param - string $path (path)
		 * @param - boolean $mode (keep empty string directory) : Default false
		 * @return - string|boolean
		 * @usage - hpl_path::clean($path,$mode);
		 */
		public static function clean($path = null, $mode = false) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0) && !hpl_func_arg :: bool2error(1)) {
				$path = self :: norm($path);
				if (self :: is_absolute($path)) {
					$uriPath = parse_url($path, PHP_URL_PATH);
					if ($uriPath) {
						if ($uriPath == '//') {
							$schemeLen = strpos($path, '://', 0) + 3;
							$divide = $schemeLen +strpos(substr($path, $schemeLen), '//', 0);
						} else {
							$divide = strpos($path, $uriPath, 0);
						}
						return substr($path, 0, $divide) . self :: clean_part($uriPath, $mode) . substr($path, $divide +strlen($uriPath));
					} else {
						return $path;
					}
				} else {
					$path = self :: clean_part((self :: is_root_model($path) ? substr($path, strlen(self :: document_root())) : $path), $mode);
					return (strlen($path) > 0 ? (!self :: is_relative($path) ? substr($path, 1) : $path) : '/');
				}
			}
			return false;
		}
		/** Get document root directory path.
		 * @access - public function
		 * @return - string|boolean
		 * @usage - hpl_path::document_root();
		 */
		public static function document_root() {
			if (!hpl_func_arg :: delimit2error() && isset ($_SERVER['DOCUMENT_ROOT'])) {
				$path = self :: norm($_SERVER['DOCUMENT_ROOT']);
				return (substr($path, -1, 1) !== '/' ? $path . '/' : $path);
			}
			return false;
		}
		/** Check path format is relative path.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - boolean
		 * @usage - hpl_path::is_relative($path);
		 */
		public static function is_relative($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				return (strpos($path, ':') === false ? true : false);
			}
			return false;
		}
		/** Check path format is absolute path.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - boolean
		 * @usage - hpl_path::is_absolute($path);
		 */
		public static function is_absolute($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				return (bool) filter_var($path, FILTER_VALIDATE_URL);
			}
			return false;
		}
		/** Check path format is document root model path.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - boolean
		 * @usage - hpl_path::is_root_model($path);
		 */
		public static function is_root_model($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				$path = self :: norm($path);
				$path = (substr($path, -1, 1) !== '/' ? $path . '/' : $path);
				$root = self :: document_root();
				if (strpos($path, $root) === 0) {
					return true;
				}
			}
			return false;
		}
		/** Check path format is file path.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - boolean
		 * @usage - hpl_path::is_files($path);
		 */
		public static function is_files($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (self :: is_absolute($path)) {
					return true;
				} else {
					return (substr(self :: clean($path), -1, 1) === '/' ? false : true);
				}
			}
			return false;
		}
		/** Check path is self current script location.
		 * @access - public function
		 * @param - string $path (path)
		 * @return - boolean
		 * @usage - hpl_path::is_self($path);
		 */
		public static function is_self($path = null) {
			if (!hpl_func_arg :: delimit2error() && !hpl_func_arg :: string2error(0)) {
				if (isset ($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $_SERVER['SCRIPT_NAME']) && self :: is_absolute($path)) {
					$path = self :: arrive($path);
					return (parse_url($path, PHP_URL_HOST) === $_SERVER['SERVER_NAME'] && parse_url($path, PHP_URL_PATH) && self :: clean(parse_url($path, PHP_URL_PATH)) === self :: clean($_SERVER['SCRIPT_NAME']) && ((!parse_url($path, PHP_URL_PORT) && ($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443')) || (parse_url($path, PHP_URL_PORT) === $_SERVER['SERVER_PORT'])) ? true : false);
				}
				elseif (isset ($_SERVER['SCRIPT_FILENAME']) && (self :: is_root_model($path) || self :: is_relative($path))) {
					if (self :: is_root_model($path)) {
						$path = self :: arrive($path);
						return (self :: clean($path) == self :: clean(strpos(self :: norm($_SERVER['SCRIPT_FILENAME']), '//') === 0 ? '/' . ltrim(self :: norm($_SERVER['SCRIPT_FILENAME']), '/') : $_SERVER['SCRIPT_FILENAME']) ? true : false);
					} else {
						$path = self :: arrive($path);
						$path = explode('/', $path);
						$path = array_reverse($path);
						$script = self :: clean(strpos(self :: norm($_SERVER['SCRIPT_FILENAME']), '//') === 0 ? '/' . ltrim(self :: norm($_SERVER['SCRIPT_FILENAME']), '/') : $_SERVER['SCRIPT_FILENAME']);
						$script = explode('/', $script);
						$script = array_reverse($script);
						$layer = 0;
						foreach ($path as $sort => $part) {
							if (!preg_match('/^(\.\.|\.)$/', $part) && (!isset ($script[$sort]) || $script[$sort] !== $part)) {
								return false;
							}
							elseif ($sort > 0 && !preg_match('/^(\.\.|\.)$/', $part) && $script[$sort] === $part) {
								$layer++;
							}
							elseif (preg_match('/^\.\.$/', $part)) {
								$layer--;
								if ($layer < 0) {
									return false;
								}
							}
							elseif ($sort == 1 && preg_match('/^\.$/', $part)) {
								return true;
							}
						}
						return ($layer == 0 ? true : false);
					}
				}
			}
			return false;
		}
	}
}
?>