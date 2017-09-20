<?php
/*
>> Information

	Title		: hpl_func_arg function
	Revision	: 1.6.1
	Notes		:

	Revision History:
	When			Create		When		Edit		Description
	---------------------------------------------------------------------------
	07-27-2016		Poen		07-28-2016	Poen		Create the program.
	07-29-2016		Poen		07-29-2016	Poen		Use error function throw an error.
	08-03-2016		Poen		08-03-2016	Poen		Add delimit2error function.
	08-05-2016		Poen		08-05-2016	Poen		Check the amount of defined arguments.
	08-17-2016		Poen		08-17-2016	Poen		Debug returns any internal error is true.
	11-22-2016		Poen		11-22-2016	Poen		Debug the program error messages.
	03-22-2017		Poen		09-19-2017	Poen		Improve the program.
	03-22-2017		Poen		03-22-2017	Poen		Fix the program error message.
	---------------------------------------------------------------------------

>> About

	Check the variable types from a user-defined function's argument list.

	You can use the argument number for data detection.

	Throw an error by error_reporting control, and save the log records.

	Logs is sent to PHP's system logger, using the Operating System's system logging mechanism or a file,
	depending on what the error_log configuration directive is set.

>> Error

	Generates a warning if called from outside of a user-defined function,
	or if argument number is greater than the number of arguments actually passed.

>> Usage Function

	==============================================================
	Include file
	Usage : include('func_arg/main.inc.php');
	==============================================================

	==============================================================
	Check the amount of defined arguments and throw an error.
	Usage : hpl_func_arg::delimit2error();
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::delimit2error())
		{
			echo 'Exceed the number of parameters defined.';
		}
		else
		{
			echo 'Approved amount.';
		}
	}
	==============================================================

	==============================================================
	Check the array type and throw an error.
	Usage : hpl_func_arg::array2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::array2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the boolean type and throw an error.
	Usage : hpl_func_arg::bool2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::bool2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the double type and throw an error.
	Usage : hpl_func_arg::double2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::double2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the integer type and throw an error.
	Usage : hpl_func_arg::int2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::int2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the NULL type and throw an error.
	Usage : hpl_func_arg::null2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::null2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the numeric type and throw an error.
	Usage : hpl_func_arg::numeric2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::numeric2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the object type and throw an error.
	Usage : hpl_func_arg::object2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::object2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the resource type and throw an error.
	Usage : hpl_func_arg::resource2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::resource2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

	==============================================================
	Check the string type and throw an error.
	Usage : hpl_func_arg::string2error($var);
	Param : integer $var (number id)
	Return : boolean
	--------------------------------------------------------------
	Example :
	function test($var=null)
	{
		if(hpl_func_arg::string2error(0))
		{
			echo 'Error Type : '.strtolower(gettype($var));
		}
		else
		{
			echo 'Confirm Type : '.strtolower(gettype($var));
		}
	}
	==============================================================

*/
?>