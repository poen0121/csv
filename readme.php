<?php
/*
>> Information

	Title		: hpl_csv function
	Revision	: 3.9.3
	Notes		: Excel CSV has 65,536 rows and 256 columns limit.

	Revision History:
	When			Create		When		Edit		Description
	---------------------------------------------------------------------------
	07-01-2010		Poen		07-01-2010	Poen		Create the program.
	08-26-2016		Poen		08-29-2016	Poen		Reforming the program.
	08-30-2016		Poen		08-30-2016	Poen		Debug gets function from fgetcsv escaping word problem.
	08-31-2016		Poen		08-31-2016	Poen		Debug gets function error message.
	09-01-2016		Poen		09-20-2016	Poen		Improve open function.
	09-01-2016		Poen		09-05-2016	Poen		Improve save function.
	09-05-2016		Poen		09-19-2016	Poen		Improve gets function.
	09-05-2016		Poen		09-05-2016	Poen		Add close function.
	09-05-2016		Poen		09-06-2016	Poen		Improve puts function.
	09-05-2016		Poen		09-05-2016	Poen		Remove save function.
	09-07-2016		Poen		09-19-2017	Poen		Improve the program.
	09-14-2016		Poen		09-14-2016	Poen		Change putCsv function name become puts.
	09-14-2016		Poen		09-14-2016	Poen		Change getCsv function name become gets.
	09-20-2016		Poen		09-20-2016	Poen		Modify puts function returns boolean.
	09-29-2016		Poen		11-21-2016	Poen		Debug the program error messages.
	09-30-2016		Poen		09-30-2016	Poen		Debug clearstatcache().
	02-22-2017		Poen		02-22-2017	Poen		Debug open function.
	02-05-2018		Poen		02-05-2018	Poen		Fix PHP 7 content function to retain original input args.
	---------------------------------------------------------------------------

>> About

	Comma separated values file processing.

	Type file extension is csv.

	Opens the file lock mode allows you to perform a simple reader/writer model which can be used on virtually every platform (including most Unix derivatives and even Windows).

	Opens the file lock mode remember to use close function release locks and resources.

	Resolving BIG5 conflicts ASCII.

>> Usage Function

	==============================================================
	Include file
	Usage : include('csv/main.inc.php');
	==============================================================

	==============================================================
	Create new Class.
	Usage : Object var name=new hpl_csv($fileLang,$sysLang);
	Param : string $fileLang (file language) : Default big5//ignore
	Param : string $sysLang (system language) : Default utf-8
	Return : object
	--------------------------------------------------------------
	Example :
	$hpl_csv=new hpl_csv();
	Example :
	$hpl_csv=new hpl_csv('big5//ignore','utf-8');
	==============================================================

	==============================================================
	Opens localhost file.
	Usage : Object->open($path,$lock);
	Param : string $path (file path)
	Param : string $mode (mode to the stream r,w,a)
	$mode :
	'r' 	>> Open for reading only; place the file pointer at the beginning of the file.
	'w' 	>> Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it.
	'a' 	>> Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it. In this mode, fseek() has no effect, writes are always appended.
	Param : boolean $lock (lock file mode) : Default true
	Return : resource
	Return Note : Returns FALSE on error.
	--------------------------------------------------------------
	Example :
	$hpl_csv->open('test.csv','w');
	==============================================================

	==============================================================
	Format line as CSV and write to file pointer.
	Usage : Object->puts(&$handle,$data);
	Param : string &$handle (a valid file pointer)
	Param : array $data (once array data)
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example :
	$fp=$hpl_csv->open('test.csv','w');
	$hpl_csv->puts($fp,array('test','1'));
	Output >> TRUE
	==============================================================

	==============================================================
	Gets line from file pointer and parse for CSV fields.
	Usage : Object->gets(&$handle,$length,$delimiter,$enclosure);
	Param : string &$handle (a valid file pointer)
	Param : string $length (must be greater than the longest line (in characters) to be found in the CSV file (allowing for trailing line-end characters). It became optional in PHP 5. Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later) the maximum line length is not limited, which is slightly slower) : Default 0
	Param : string $delimiter (the optional delimiter parameter sets the field delimiter (one character only)) : Default ,
	Param : string $enclosure (the optional enclosure parameter sets the field enclosure character (one character only)) : Default "
	Return : array
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example :
	$fp=$hpl_csv->open('test.csv','r');
	while($data=$hpl_csv->gets($fp))
	{
		print_r($data);
	}
	==============================================================

	==============================================================
	Closes an open file pointer.
	Usage : Object->close(&$handle);
	Param : string &$handle (a valid file pointer)
	Return : boolean
	Return Note : Returns FALSE on failure.
	--------------------------------------------------------------
	Example :
	$fp=$hpl_csv->open('test.csv','w');
	$hpl_csv->puts($fp,array('test','1'));
	$hpl_csv->close($fp);
	Output >> TRUE
	Example :
	$fp=$hpl_csv->open('test.csv','r');
	while($data=$hpl_csv->gets($fp))
	{
		print_r($data);
	}
	$hpl_csv->close($fp);
	Output >> TRUE
	==============================================================

>> Example

	//Save File
	$hpl_csv=new hpl_csv('big5//ignore','utf-8');
	$fp=$hpl_csv->open('test.csv','w');
	$hpl_csv->puts($fp,array('test','1'));
	$hpl_csv->puts($fp,array('test','2'));
	$hpl_csv->puts($fp,array('test','3'));
	$hpl_csv->close($fp);

	//Load File
	$fp=$hpl_csv->open('test.csv','r');
	while($data=$hpl_csv->gets($fp))
	{
		print_r($data);
	}
	$hpl_csv->close($fp);

*/
?>