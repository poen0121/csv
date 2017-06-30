<?php
/*
>> Information

	Title		: hpl_error function
	Revision	: 2.9.2
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
	12-05-2016		Poen		06-30-2017	Poen		Improve the program.
	03-09-2017		Poen		03-15-2017	Poen		Modify cast function to add stack trace.
	03-15-2017		Poen		03-15-2017	Poen		Debug cast function uses the wrong error level to throw a file line error.
	03-22-2017		Poen		03-22-2017	Poen		Fix cast function error message.
	04-20-2017		Poen		04-20-2017	Poen		Support CLI normal error output.
	06-21-2017		Poen		06-21-2017	Poen		Fix error log time and line breaks.
	06-22-2017		Poen		06-22-2017	Poen		Add peel error log mechanism.
	---------------------------------------------------------------------------

>> About

	Throw an error by error_reporting control, and save the log records.

	Automatically grab the file and line echo location can be used to improve the depth of the actual stratification.

	Logs is sent to PHP's system logger, using the Operating System's system logging mechanism or a file,
	depending on what the error_log configuration directive is set.

	Set php.ini display_errors control display error message.

	Set php.ini log_errors control save error message.

>> Peel Error Logs

	Use $_SERVER['PEEL_OFF_ERROR_LOG_FILE'] to save the peel off error log file location.

	Use $_SERVER['PEEL_OFF_NAME'] to save the peel off name.

>> Stack Trace

	Switch variable parameter is $_SERVER['ERROR_STACK_TRACE'] , stack trace calls will consume memory.

	Stack trace grab file and line echo location.

	Enable : $_SERVER['ERROR_STACK_TRACE'] = On;

	Disable : $_SERVER['ERROR_STACK_TRACE'] = Off;

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
	Throws an error and saves the error log.
	Usage : hpl_error::cast($errorMessage,$errno,$echoDepth,$logTitle);
	Param : string $errorMessage (error message)
	Param : integer $errno (error level by error_reporting) : Default E_USER_NOTICE
	Param : integer $echoDepth (location echo depth) : Default 0
	Param : string $logTitle (log title) : Default 'PHP'
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		hpl_error::cast('Test Error',E_USER_WARNING);//get location on line
	}
	test();
	Example :
	function test($var=null)
	{
		hpl_error::cast('Test Error',E_USER_WARNING,1);
	}
	test();//get location on line
	==============================================================

*/
?>