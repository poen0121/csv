<?php
/*
>> Information

	Title		: hpl_path function
	Revision	: 3.10.10
	Notes		: You can use chdir() change current script parent directories.

	Revision History:
	When			Create		When		Edit		Description
	---------------------------------------------------------------------------
	04-02-2010		Poen		05-28-2015	Poen		Create the program.
	08-02-2016		Poen		08-02-2016	Poen		Reforming the program.
	08-16-2016		Poen		08-16-2016	Poen		Add is_root_model function.
	08-22-2016		Poen		08-22-2016	Poen		Add full function.
	08-22-2016		Poen		08-22-2016	Poen		Improve relative function.
	08-22-2016		Poen		08-15-2018	Poen		Improve absolute function.
	08-22-2016		Poen		08-22-2016	Poen		Improve parent function.
	08-22-2016		Poen		08-22-2016	Poen		Change parent function name become cut_root.
	08-23-2016		Poen		08-23-2016	Poen		Remove cut_root function.
	08-23-2016		Poen		08-23-2016	Poen		Add cutdir function.
	08-24-2016		Poen		08-24-2016	Poen		Add relative_layer_count function.
	08-24-2016		Poen		08-24-2016	Poen		Add full_relative function.
	08-24-2016		Poen		08-24-2016	Poen		Add nexus_full_relative function.
	08-25-2016		Poen		08-25-2016	Poen		Change full function name become script.
	09-08-2016		Poen		09-26-2017	Poen		Improve the program.
	09-09-2016		Poen		09-09-2016	Poen		Debug nexus_full_relative function.
	09-09-2016		Poen		09-09-2016	Poen		Improve full_relative function.
	09-09-2016		Poen		09-09-2016	Poen		Improve script function.
	09-09-2016		Poen		09-09-2016	Poen		Improve relative function.
	09-10-2016		Poen		09-10-2016	Poen		Add is_relative function.
	09-10-2016		Poen		09-10-2016	Poen		Add document_root function.
	09-10-2016		Poen		09-10-2016	Poen		Add clean function.
	09-10-2016		Poen		09-10-2016	Poen		Add is_files function.
	09-19-2016		Poen		09-19-2016	Poen		Add arrive function.
	10-14-2016		Poen		10-18-2016	Poen		Improve arrive function.
	10-14-2016		Poen		10-18-2016	Poen		Improve clean function.
	10-14-2016		Poen		10-14-2016	Poen		Improve is_files function.
	10-18-2016		Poen		10-18-2016	Poen		Add is_self function.
	10-19-2016		Poen		04-27-2017	Poen		Improve is_self function.
	02-22-2017		Poen		05-08-2017	Poen		Debug is_self function.
	02-22-2017		Poen		02-22-2017	Poen		Debug arrive function.
	02-22-2017		Poen		02-22-2017	Poen		Debug clean function.
	03-27-2017		Poen		03-27-2017	Poen		Fix nexus_full_relative function error message.
	04-27-2017		Poen		04-27-2017	Poen		Debug document_root function.
	04-27-2017		Poen		04-28-2017	Poen		Debug absolute function.
	02-06-2018		Poen		02-06-2018	Poen		Fix PHP 7 content function to retain original input args.
	08-08-2018		Poen		08-08-2018	Poen		Fix arrive function argument type judgment.
	---------------------------------------------------------------------------

>> About

	Processing file path.

>> Usage Function

	==============================================================
	Include file
	Usage : include('path/main.inc.php');
	==============================================================

	==============================================================
	Change path to norm path.
	Usage : hpl_path::norm($path);
	Param : string $path (path)
	Return : string
	--------------------------------------------------------------
	Example :
	hpl_path::norm('\var\www\index.php');
	Output >> /var/www/index.php
	==============================================================

	==============================================================
	Count actual arrive path's `../` relative layer number.
	Usage : hpl_path::relative_layer_count($path);
	Param : string $path (relative path)
	Return : integer
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative_layer_count('index.php');
	Output >> 0
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative_layer_count('../index.php');
	Output >> 1
	==============================================================

	==============================================================
	Get the host path's nexus relative full path based on the current script and document root target script.
	Usage : hpl_path::nexus_full_relative($path,$script_path);
	Param : string $path (path)
	Param : string $scriptName (script name)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::nexus_full_relative('index.php','deep/test/script.php');
	Output >> ../../path/index.php ( Position : /var/www/path/index.php )
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::nexus_full_relative('../index.php','deep/test/script.php');
	Output >> ../../index.php ( Position : /var/www/index.php )
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::nexus_full_relative(__FILE__,'deep/test/script.php');
	Output >> ../../path/index.php ( Position : /var/www/path/index.php )
	hpl_path::nexus_full_relative('../../index.php','deep/test/script.php');
	Output >> ../../../index.php ( Position : /var/index.php )
	==============================================================

	==============================================================
	Get the host path's relative full path based on the current script.
	Usage : hpl_path::full_relative($path);
	Param : string $path (path)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::full_relative('index.php');
	Output >> ../path/index.php ( Position : /var/www/path/index.php )
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::full_relative('../index.php');
	Output >> ../index.php ( Position : /var/www/index.php )
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::full_relative(__FILE__);
	Output >> ../path/index.php ( Position : /var/www/path/index.php )
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::full_relative('../../index.php');
	Output >> ../../index.php ( Position : /var/index.php )
	==============================================================

	==============================================================
	Get the host path's script name based on the current script and document root.
	Usage : hpl_path::script($path);
	Param : string $path (path)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::script('index.php');
	Output >> /path/index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::script('../index.php');
	Output >> /index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::script(__FILE__);
	Output >> /path/index.php
	==============================================================

	==============================================================
	Get the target script's relative path based on the current script and document root.
	Usage : hpl_path::relative($scriptName);
	Param : string $scriptName (script name)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative('index.php');
	Output >> ../index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative('../../index.php');
	Output >> ../index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative('./index.php');
	Output >> ../index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::relative(__FILE__);
	Output >> ../path/index.php
	==============================================================

	==============================================================
	Get the target script's absolute path based on the document root.
	Usage : hpl_path::absolute($scriptName);
	Param : string $scriptName (script name)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : http://example & __FILE__ >> /var/www/path/index.php
	hpl_path::absolute('index.php');
	Output >> http://example/index.php
	Example : http://example & __FILE__ >> /var/www/path/index.php
	hpl_path::absolute('../index.php');
	Output >> http://example/index.php
	Example : http://example & __FILE__ >> /var/www/path/index.php
	hpl_path::absolute(__FILE__);
	Output >> http://example/path/index.php
	==============================================================

	==============================================================
	Returns a parent directory's script name based on the document root.
	Usage : hpl_path::cutdir($scriptName);
	Param : string $scriptName (script name)
	Return : string
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::cutdir('index.php');
	Output >> /
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::cutdir('../../index.php');
	Output >> /
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::cutdir(__FILE__);
	Output >> /path/
	==============================================================

	==============================================================
	Returns the full path to arrive.
	Usage : hpl_path::arrive($path,$mode);
	Param : string $path (path)
	Param : boolean $mode (keep empty string directory) : Default false
	Return : string
	--------------------------------------------------------------
	Example :
	hpl_path::arrive('../index.php');
	Output >> ../index.php
	Example :
	hpl_path::arrive('');
	Output >> ./
	Example :
	hpl_path::arrive('../path/../index.php');
	Output >> ../index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::arrive(__FILE__);
	Output >> ./path/index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::arrive(__DIR__.'/../index.php');
	Output >> ./index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::arrive(__DIR__.'/../../index.php');
	Output >> ../index.php
	Example :
	hpl_path::arrive('http://example.com/path/../index.php');
	Output >> http://example.com/index.php
	Example :
	hpl_path::arrive('http://example.com/./index.php');
	Output >> http://example.com/index.php
	Example :
	hpl_path::arrive('http://example.com/////index.php');
	Output >> http://example.com/index.php
	==============================================================

	==============================================================
	Get the correct full path script name.
	Usage : hpl_path::clean($path,$mode);
	Param : string $path (path)
	Param : boolean $mode (keep empty string directory) : Default false
	Return : string
	--------------------------------------------------------------
	Example :
	hpl_path::clean('../../../index.php');
	Output >> /index.php
	Example :
	hpl_path::clean('');
	Output >> /
	Example :
	hpl_path::clean('../path/../index.php');
	Output >> /path/index.php
	Example :
	hpl_path::clean('../path/////index.php');
	Output >> /path/index.php
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::clean(__DIR__.'/../../../index.php');
	Output >> /path/index.php
	Example :
	hpl_path::clean('http://example.com/../index.php');
	Output >> http://example.com/index.php
	Example :
	hpl_path::clean('http://example.com/./index.php');
	Output >> http://example.com/index.php
	Example :
	hpl_path::clean('http://example.com/////index.php');
	Output >> http://example.com/index.php
	==============================================================

	==============================================================
	Get document root directory path.
	Usage : hpl_path::document_root();
	Return : string
	--------------------------------------------------------------
	Example :
	hpl_path::document_root();
	==============================================================

	==============================================================
	Check path format is relative path.
	Usage : hpl_path::is_relative($path);
	Param : string $path (path)
	Return : boolean
	--------------------------------------------------------------
	Example :
	hpl_path::is_relative('index.php');
	Output >> TRUE
	Example :
	hpl_path::is_relative('/index.php');
	Output >> TRUE
	Example :
	hpl_path::is_relative('./index.php');
	Output >> TRUE
	Example :
	hpl_path::is_relative('../index.php');
	Output >> TRUE
	==============================================================

	==============================================================
	Check path format is absolute path.
	Usage : hpl_path::is_absolute($path);
	Param : string $path (path)
	Return : boolean
	--------------------------------------------------------------
	Example :
	hpl_path::is_absolute('http://example/index.php');
	Output >> TRUE
	==============================================================

	==============================================================
	Check path format is document root model path.
	Usage : hpl_path::is_root_model($path);
	Param : string $path (path)
	Return : boolean
	--------------------------------------------------------------
	Example :
	hpl_path::is_root_model(__FILE__);
	Output >> TRUE
	==============================================================

	==============================================================
	Check path format is file path.
	Usage : hpl_path::is_files($path);
	Param : string $path (path)
	Return : boolean
	--------------------------------------------------------------
	Example :
	hpl_path::is_files(__FILE__);
	Output >> TRUE
	Example :
	hpl_path::is_files('./index.php');
	Output >> TRUE
	Example :
	hpl_path::is_files('./index.php/');
	Output >> FALSE
	Example :
	hpl_path::is_files('http://example/index.php');
	Output >> TRUE
	Example :
	hpl_path::is_files('http://example/index.php/');
	Output >> TRUE
	==============================================================

	==============================================================
	Check path is self current script location.
	Usage : hpl_path::is_self($path);
	Param : string $path (path)
	Return : boolean
	--------------------------------------------------------------
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::is_self(__FILE__);
	Output >> TRUE
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::is_self('./index.php');
	Output >> TRUE
	Example : __FILE__ >> /var/www/path/index.php
	hpl_path::is_self('./index.php/');
	Output >> FALSE
	Example : http://example & __FILE__ >> /var/www/path/index.php
	hpl_path::is_self('http://example/index.php');
	Output >> FALSE
	Example : http://example & __FILE__ >> /var/www/path/index.php
	hpl_path::is_self('http://example/path/index.php');
	Output >> TRUE
	==============================================================

*/
?>