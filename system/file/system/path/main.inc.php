<?php
if (!class_exists('hpl_path')) {
	include (strtr(dirname(__FILE__), '\\', '/') . '/system/func_arg/main.inc.php');
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
				return strtr($path, '\\', '/');
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
					$arrivePath = self :: arrive($path);
					$arrivePath = explode('/', $arrivePath);
					$count = 0;
					foreach ($arrivePath as $part) {
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
					$getScriptName = self :: clean($scriptName);
					$scriptPathLen = self :: relative_layer_count(self :: relative($getScriptName));
					$pathLen = self :: relative_layer_count(self :: full_relative($path));
					$ownPath = explode('/', $getScriptName);
					$ownPath = array_slice($ownPath, 1, -1);
					$layer = $pathLen - $scriptPathLen; //more upper layers
					$arrivePath = self :: arrive($path);
					return ($layer > 0 ? str_repeat('../', $layer) : '') . (count($ownPath) > 0 ? str_repeat('../', count($ownPath)) : ($layer > 0 ? '' : './')) . substr((!$isRootModel ? self :: script($arrivePath) : self :: clean($arrivePath)), 1);
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
						$arrivePath = self :: arrive($path);
						$layer = self :: relative_layer_count($arrivePath) - count($ownPath);
						$layer = ($isRootModel ? count($ownPath) + $layer : $layer); //more upper layers
						return ($layer > 0 ? str_repeat('../', $layer) : '') . (count($ownPath) > 0 ? str_repeat('../', count($ownPath)) : ($layer > 0 ? '' : './')) . substr((!$isRootModel ? self :: script($arrivePath) : self :: clean($arrivePath)), 1);
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
							$arrivePath = self :: arrive($path);
							$cut = self :: relative_layer_count($arrivePath); //for cut own path
							$arrivePath = self :: clean($arrivePath);
							$suffixPath = explode('/', $arrivePath);
							$suffixPath = array_slice($suffixPath, 1);
							$arrivePath = implode('/', array_merge(array_slice($ownPath, 1, - ($cut +1)), $suffixPath));
							return (substr($arrivePath, 0, 1) !== '/' ? '/' . $arrivePath : $arrivePath);
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
				elseif (isset ($_SERVER['REQUEST_SCHEME'] { 0 }, $_SERVER['SERVER_NAME'] { 0 }, $_SERVER['SERVER_PORT'] { 0 }) && is_string($_SERVER['REQUEST_SCHEME']) && is_string($_SERVER['SERVER_NAME']) && is_string($_SERVER['SERVER_PORT'])) {
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
			if (isset ($path { 0 })) {
				$pathInfo = explode('/', $path);
				$pathInfo = array_reverse($pathInfo);
				$end = (count($pathInfo) - 1);
				$layer = 0;
				$keepPath = array ();
				foreach ($pathInfo as $sort => $part) {
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
								if (isset ($part { 0 }) || $sort < $end) {
									$keepPath[] = $part;
								}
							} else {
								if ($sort < $end || (isset ($part { 0 }) && $sort == $end)) {
									$layer--;
								}
							}
						}
					}
				}
				$keepPath = array_reverse($keepPath);
				$pathInfo = ($layer > 0 ? str_repeat('../', $layer) : './') . implode('/', $keepPath);
				return ($mode ? $pathInfo : preg_replace('/(\/)+/', '/', $pathInfo));
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
				$normPath = self :: norm($path);
				if (self :: is_absolute($normPath)) {
					$uriPath = parse_url($normPath, PHP_URL_PATH);
					if ($uriPath) {
						if ($uriPath == '//') {
							$schemeLen = strpos($normPath, '://', 0) + 3;
							$divide = $schemeLen +strpos(substr($normPath, $schemeLen), '//', 0);
						} else {
							$divide = strpos($normPath, $uriPath, 0);
						}
						return substr($normPath, 0, $divide) . self :: clean(self :: arrive_part($uriPath, $mode)) . substr($normPath, $divide +strlen($uriPath));
					} else {
						return $normPath;
					}
				}
				elseif (self :: is_root_model($normPath)) {
					$normPath = substr($normPath, strlen(self :: document_root()));
				}
				elseif (!self :: is_relative($normPath)) {
					$uriScheme = strstr($normPath, ':', -1);
					$normPath = substr($normPath, strlen($uriScheme) + 1);
					return $uriScheme . ':' . self :: clean(self :: arrive_part($normPath, $mode));
				}
				return self :: arrive_part($normPath, $mode);
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
			if (isset ($path { 0 })) {
				$pathInfo = explode('/', $path);
				$end = (count($pathInfo) - 1);
				$cleanPath = '';
				foreach ($pathInfo as $sort => $part) {
					if (preg_match('/^(\.\.|\.)$/', $part)) {
						$cleanPath .= ($sort == 0 ? '/' : '');
					} else {
						if (!isset ($part { 0 })) {
							$cleanPath .= (!$mode || $sort == $end ? '' : '/');
						} else {
							$cleanPath .= $part . ($sort < $end ? '/' : '');
						}
					}
				}
			}
			return (isset ($cleanPath { 0 }) ? (substr($cleanPath, 0, 1) !== '/' ? '/' : '') . $cleanPath : '/');
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
				$normPath = self :: norm($path);
				if (self :: is_absolute($normPath)) {
					$uriPath = parse_url($normPath, PHP_URL_PATH);
					if ($uriPath) {
						if ($uriPath == '//') {
							$schemeLen = strpos($normPath, '://', 0) + 3;
							$divide = $schemeLen +strpos(substr($normPath, $schemeLen), '//', 0);
						} else {
							$divide = strpos($normPath, $uriPath, 0);
						}
						return substr($normPath, 0, $divide) . self :: clean_part($uriPath, $mode) . substr($normPath, $divide +strlen($uriPath));
					} else {
						return $normPath;
					}
				} else {
					$normPath = self :: clean_part((self :: is_root_model($normPath) ? substr($normPath, strlen(self :: document_root())) : $normPath), $mode);
					return (isset ($normPath { 0 }) ? (!self :: is_relative($normPath) ? substr($normPath, 1) : $normPath) : '/');
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
			if (!hpl_func_arg :: delimit2error() && isset ($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT']) ) {
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
				$normPath = self :: norm($path);
				$normPath = (substr($normPath, -1, 1) !== '/' ? $normPath . '/' : $normPath);
				$root = self :: document_root();
				if (strpos($normPath, $root) === 0) {
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
				if (isset ($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], $_SERVER['SCRIPT_NAME']) && is_string($_SERVER['SERVER_NAME']) && is_string($_SERVER['SERVER_PORT']) && is_string($_SERVER['SCRIPT_NAME']) && self :: is_absolute($path)) {
					$arrivePath = self :: arrive($path);
					return (parse_url($arrivePath, PHP_URL_HOST) === $_SERVER['SERVER_NAME'] && parse_url($arrivePath, PHP_URL_PATH) && self :: clean(parse_url($arrivePath, PHP_URL_PATH)) === self :: clean($_SERVER['SCRIPT_NAME']) && ((!parse_url($arrivePath, PHP_URL_PORT) && ($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443')) || (parse_url($arrivePath, PHP_URL_PORT) === $_SERVER['SERVER_PORT'])) ? true : false);
				}
				elseif (isset ($_SERVER['SCRIPT_FILENAME']) && is_string($_SERVER['SCRIPT_FILENAME']) && (self :: is_root_model($path) || self :: is_relative($path))) {
					if (self :: is_root_model($path)) {
						$arrivePath = self :: arrive($path);
						return (self :: clean($arrivePath) == self :: clean(realpath($_SERVER['SCRIPT_FILENAME'])) ? true : false);
					} else {
						$arrivePath = self :: arrive($path);
						$arrivePath = explode('/', $arrivePath);
						$arrivePath = array_reverse($arrivePath);
						$script = self :: clean(realpath($_SERVER['SCRIPT_FILENAME']));
						$script = explode('/', $script);
						$script = array_reverse($script);
						$layer = 0;
						foreach ($arrivePath as $sort => $part) {
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