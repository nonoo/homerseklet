<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL ^ E_NOTICE);
	ob_start('ob_gzhandler');

	header("Content-type: text/html; charset=UTF-8");
	header("Pragma: no-cache");
	header("Expires: 0");

	include('config.inc.php');
	include('homerseklet.inc.php');

	$context = $_GET['context'];
	$interval = $_GET['interval'];

	if (getContextName($context) == 'Unknown')
		$context = 'tata';
	if (getIntervalName($interval) == 'Unknown')
		$interval = '3d';

	$db = new mysqli($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName']);
	if (!$db)
		die("Unable to init database.");

	switch ($interval) {
		default:
		case '3d': $t = strtotime('-3 days'); break;
		case '1w': $t = strtotime('-1 week'); break;
		case '1m': $t = strtotime('-1 month'); break;
		case '3m': $t = strtotime('-3 months'); break;
		case '1y': $t = strtotime('-1 year'); break;
		case '5y': $t = strtotime('-5 years'); break;
	}

	$subcontexts = getSubcontexts($context);
	echo "Date";
	foreach ($subcontexts as $subcontext)
		echo ', ' . getSubcontextTitle($subcontext);
	echo "\n";

	$query = "(select `date`, `value` as `$subcontexts[0]-value`";
	for ($i = 0; $i < count($subcontexts)-1; $i++)
		$query .= ", null";
	$query .= " from `$subcontexts[0]` where unix_timestamp(`date`) > $t)";
	for ($i = 1; $i < count($subcontexts); $i++) {
		$query .= " union (select `date`";
		for ($j = 0; $j < $i; $j++)
			$query .= ", null";
		$query .= ", `value` as `$subcontexts[$i]-value` from `$subcontexts[$i]`";
		for ($j = $i; $j < count($subcontexts)-1; $j++)
			$query .= ", null";
		$query .= " where unix_timestamp(`date`) > $t)";
	}
	$query .= " order by `date`";

	if (($res = $db->query($query)) === FALSE)
		echo "error executing db query: $query\nerror: " . mysqli_error() . "\n";

	while ($row = $res->fetch_row()) {
		echo "$row[0]";
		for ($i = 1; $i < count($subcontexts)+1; $i++)
			echo ", $row[$i]";
		echo "\n";
	}
?>
