<?php
	include('config.inc.php');

	if ($_GET['p'] != $config['postPassword'])
		die('403 Forbidden');

	include('homerseklet.inc.php');

	$context = $_GET['c'];
	$ts = $_GET['t'];
	$value = $_GET['v'];

	if (getContextName($context) == 'Unknown')
		die('404 Not Found');

	if (!is_numeric($ts) || !is_numeric($value))
		die('400 Bad Request');

    mysql_connect($config['dbHost'], $config['dbUser'], $config['dbPass']);
    @mysql_select_db($config['dbName']) or die("unable to select database.");

    $query = 'insert into `' . mysql_escape_string($context) . '` (`date`, `value`) values (from_unixtime("' . mysql_escape_string($ts) . '"), "' . mysql_escape_string($value) . '")';
	if (!mysql_query($query))
		echo "error executing db query: $query\nerror: " . mysql_error() . "\n";
    mysql_close();

    echo "ok\n";
?>
