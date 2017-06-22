# csv
PHP Library ( PHP >= 5.2 ) CLI,CGI

> About

	Comma separated values file processing.

	Type file extension is csv.

	Opens the file lock mode allows you to perform a simple reader/writer model which can be used on virtually every platform (including most Unix derivatives and even Windows).

	Opens the file lock mode remember to use close function release locks and resources.

	Resolving BIG5 conflicts ASCII.

> Learning Documents

	Please read `readme.php` document.

> Example

	Save File
	--------------------------------------------------------------
	$hpl_csv=new hpl_csv('big5//ignore','utf-8');
	$fp=$hpl_csv->open('test.csv','w');
	$hpl_csv->puts($fp,array('test','1'));
	$hpl_csv->puts($fp,array('test','2'));
	$hpl_csv->puts($fp,array('test','3'));
	$hpl_csv->close($fp);

	Load File
	--------------------------------------------------------------
	$fp=$hpl_csv->open('test.csv','r');
	while($data=$hpl_csv->gets($fp))
	{
		print_r($data);
	}
	$hpl_csv->close($fp);