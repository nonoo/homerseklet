<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL ^ E_NOTICE);

	include('config.inc.php');

	if ($_GET['p'] != $config['postPassword'])
		die('403 Forbidden');

	include('homerseklet.inc.php');

	$context = $_GET['c'];
	$ts = $_GET['t'];
	$value = $_GET['v'];

	$db = new mysqli($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);
	if (!$db)
		die("Unable to init database.");

	$query = "show tables from `${config['dbName']}` where `Tables_in_${config['dbName']}` = '$context'";
	if (($res = $db->query($query)) === FALSE)
		echo "error executing db query: $query\nerror: " . $db->error . "\n";
	if (!$res->fetch_row())
		die('404 Not Found');

	if (!is_numeric($ts) || !is_numeric($value))
		die('400 Bad Request');

    $query = 'insert into `' . $db->escape_string($context) . '` (`date`, `value`) values (from_unixtime("' . $db->escape_string($ts) . '"), "' . $db->escape_string($value) . '")';
	if (!$db->query($query))
		echo "error executing db query: $query\nerror: " . $db->error . "\n";

    echo "ok\n";
?>
