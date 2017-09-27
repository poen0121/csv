<?php
/*
>> Information

	Title		: hpl_error function
	Revision	: 2.14.4
	Notes		:

	Revision History:
	When			Create		When		Edit		Description
	---------------------------------------------------------------------------
	07-29-2016		Poen		07-29-2016	Poen		Create the program.
	08-02-2016		Poen		08-02-2016	Poen		Line to increase "<br />\n" end.
	08-03-2016		Poen		08-03-2016	Poen		Join display_errors mechanism.
	08-03-2016		Poen		08-03-2016	Poen		Join E_USER_ERROR interrupt exit.
	08-05-2016		Poen		08-05-2016	Poen		Check the amount of defined arguments.
	08-18-2016		Poen		08-18-2016	Poen		Check the log_errors status to save logs.
	09-23-2016		Poen		09-23-2016	Poen		Debug cast function.
	11-21-2016		Poen		11-21-2016	Poen		Modify the usage error level by error_reporting.
	11-22-2016		Poen		11-22-2016	Poen		Debug the program error messages.
	12-05-2016		Poen		09-27-2017	Poen		Improve the program.
	03-09-2017		Poen		03-15-2017	Poen		Modify cast function to add stack trace.
	03-15-2017		Poen		03-15-2017	Poen		Debug cast function uses the wrong error level to throw a file line error.
	03-22-2017		Poen		03-22-2017	Poen		Fix cast function error message.
	04-20-2017		Poen		04-20-2017	Poen		Support CLI normal error output.
	06-21-2017		Poen		06-21-2017	Poen		Fix error log time and line breaks.
	06-22-2017		Poen		06-22-2017	Poen		Add peel error log mechanism.
	09-13-2017		Poen		09-13-2017	Poen		Add recessive signal message to the error_handler function.
	09-13-2017		Poen		09-18-2017	Poen		Modify message to the error_handler function.
	09-13-2017		Poen		09-18-2017	Poen		Add begin function.
	09-13-2017		Poen		09-18-2017	Poen		Add error_log_file function.
	09-13-2017		Poen		09-18-2017	Poen		Add trace function.
	09-13-2017		Poen		09-18-2017	Poen		Add cast_log_title function.
	09-13-2017		Poen		09-18-2017	Poen		Add capture function.
	---------------------------------------------------------------------------

>> About

	Use error_handler to capture error messages by using the error_reporting control to throw an error and save the log records.

	Automatically grab the file and line echo location can be used to improve the depth of the actual stratification.

	Logs is sent to PHP's system logger, using the Operating System's system logging mechanism or a file,
	depending on what the error_log configuration directive is set.

	Set php.ini display_errors control display error message.

	Set php.ini log_errors control save error message.
	
	Class function stack trace is turned off by default.
	
>> Note

	If the hpl_error::cast function is used in the current error_handler function, the default error_handler function hpl_error::ErrorHandler is used.

	If the hpl_error::cast function is used in the error_handler function, the echo depth will be limited to the error_handler function range.

	If the hpl_error::cast function is used in the current error_handler function, the stack trace will be closed.
	
	If the hpl_error::capture function $exit is false, the script will not exit but the next error still stops capturing.
	
>> Peel Error Logs

	Allows the stripping of the capture error mode so that the stored information is stored 
	at the specified file location when the system archive location can not be changed.

	Usage : hpl_error::error_log_file function

>> Stack Trace

	Stack trace calls will consume memory.

	Stack trace grab file and line echo location.
	
	Usage : hpl_error::trace function

>> Error Level

	The designated error type for this error. It applies only to error_reporting,
	and will default to E_USER_NOTICE.
	-------------------------------------------------------
	Note :
	E_PARSE,E_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR
	Echo a fatal error message and interrupt EXIT.

	Recommended :
	User-Level [ E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | E_USER_DEPRECATED ]
	E_USER_ERROR : Echo a fatal error message and interrupt EXIT.
	E_USER_WARNING : Echo a warning message and return TRUE.
	E_USER_NOTICE : Echo a notice message and return TRUE.
	E_USER_DEPRECATED : Echo a deprecated message and return TRUE.

>> Usage Function

	==============================================================
	Include file
	Usage : include('error/main.inc.php');
	==============================================================

	==============================================================
	Set the user-defined error handler to start the function.
	Usage : hpl_error::begin($errorHandler);
	Param : callable $errorHandler (a callback or null default last declared value with the following signature)
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : Enable default error_handler function.
	hpl_error::begin();
	Output >> TRUE
	Example : Enable definition error_handler function.
	function ErrorHandler() {
		return hpl_error::capture();
	}
	hpl_error::begin('ErrorHandler');
	trigger_error('Test Error',E_USER_WARNING);
	Output >> TRUE
	==============================================================
	
	==============================================================
	Set PHP log errors to specified default file.
	Usage : hpl_error::error_log_file($path,$peel);
	Param : string $path (file path)
	Param : boolean $peel (allow peel off to capture the error pattern) : Default false
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : Rewrite the PHP system error_log file and terminate the error logs to strip the system error log file.
	hpl_error::error_log_file('./test.log');
	Output >> TRUE
	Example : Rewrite the PHP system error_log file and terminate the error logs to strip the system error log file.
	hpl_error::error_log_file('./test_log',false);
	Output >> TRUE
	Example : Allow the error logs to strip the system error log file.
	hpl_error::error_log_file('./test_log',true);
	Output >> TRUE
	Example : Error file path.
	hpl_error::error_log_file('http://example/test_log');
	Output >> FALSE
	Example : Error file path.
	hpl_error::error_log_file('./');
	Output >> FALSE
	==============================================================
	
	==============================================================
	Set the error stack trace mode.
	Usage : hpl_error::trace($switch);
	Param : boolean $switch (open or close the stack trace error mode)
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : Close the error stack trace mode.
	hpl_error::trace(false);
	Output >> TRUE
	Example : Open the error stack trace mode.
	hpl_error::trace(true);
	Output >> TRUE
	==============================================================
		
	==============================================================
	Set the error cast function default log title.
	Usage : hpl_error::cast_log_title($default);
	Param : string $default (default error log title) : Default 'PHP' is system reserved words
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example : Log title to 'PHP-USER'.
	hpl_error::cast_log_title('PHP-USER');
	hpl_error::cast('Test Error',E_USER_WARNING);
	Output >> TRUE
	Example : Log title to 'PHP'.
	hpl_error::cast_log_title('PHP-USER');
	hpl_error::cast('Test Error',E_USER_WARNING,0,'PHP');
	Output >> TRUE
	==============================================================

	==============================================================
	Throws an error and sends a recessive signal message to the error_handler function.
	Note : The error_handler will catch the message 'ERROR_TOUCH_SIGNAL:' id string.
	Usage : hpl_error::cast($message,$errno,$echoDepth,$logTitle);
	Param : string $message (the specified error message, the length limit of more than 1024 bytes, will be truncated)
	Param : integer $errno (error level by error_reporting) : Default E_USER_NOTICE
	Param : integer $echoDepth (location echo depth) : Default 0
	Param : string $logTitle (log title) : By default, the cast_log_title function default log title is used
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		hpl_error::cast('Test Error',E_USER_WARNING);//get location on line
	}
	test();
	Output >> TRUE
	Example :
	function test($var=null)
	{
		hpl_error::cast('Test Error',E_USER_WARNING,1);
	}
	test();//get location on line
	Output >> TRUE
	==============================================================

	==============================================================
	Capture error_handler information output error.
	Usage : hpl_error::capture($exit);
	Param : boolean $exit (fatal error exit script) : Default true
	Return : boolean|null
	Return Note : Returns null on stop.
	--------------------------------------------------------------
	Example :
	function ErrorHandler() {
		return hpl_error::capture();
	}
	hpl_error::begin('ErrorHandler');
	hpl_error::cast('Test Error',E_USER_WARNING);
	Output >> TRUE
	Example :
	function ErrorHandler() {
		return hpl_error::capture();
	}
	hpl_error::begin('ErrorHandler');
	trigger_error('Test Error',E_USER_WARNING);
	Output >> TRUE
	Example :
	function ErrorHandler() {
		return hpl_error::capture(false);
	}
	hpl_error::begin('ErrorHandler');
	hpl_error::cast('Test Error',E_USER_ERROR);
	echo 'end message';
	Output >> TRUE
	==============================================================

>> Example 

	function ErrorHandler() {
		return hpl_error::capture();
	}
	hpl_error::begin('ErrorHandler');
	hpl_error::trace(true);
	function test($var=null)
	{
		hpl_error::cast('Cast Test Error',E_USER_WARNING,1);
	}
	test();//get location on line
	function test_trigger($var=null)
	{
		trigger_error('Trigger Test Error',E_USER_WARNING);//get location on line
	}
	test_trigger();

*/
?>