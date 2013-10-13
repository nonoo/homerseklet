<?php
	ini_set('display_errors','On');
	error_reporting(E_ALL ^ E_NOTICE);
	ob_start('ob_gzhandler');

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
?>
<!DOCTYPE html>
<html>
<head>
	<title>Hőmérséklet - <?php echo getContextName($context); ?></title>
	<meta charset="UTF-8" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.gif" />
	<link rel="stylesheet" type="text/css" media="all" href="homerseklet.css" />

	<script type="text/javascript" src="http://dygraphs.com/1.0.1/dygraph-combined.js"></script>
</head>

<body>
	<a id="title" href="<?php echo substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')); ?>">Hőmérséklet</a>
	<div id="contextswitchbox">
		<span id="contexts">
			Locations:
<?php
		$contexts = getContexts();
		for ($i = 0; $i < count($contexts); $i++) {
?>
			<input type="button" value="<?php echo getContextName($contexts[$i]); ?>" onclick="location.href='?context=<?php echo $contexts[$i]; ?>&interval=<?php echo $interval; ?>';"<?php if ($context == $contexts[$i]) echo ' class="selected"'; ?> />
<?php
		}
?>
		</span>
	</div>
	<div id="infobox">
		<a href="https://github.com/nonoo/homerseklet">source</a> |
		<a href="http://dp.nonoo.hu/measuring-temperature/">info</a> |
		<a href="http://webcam.nonoo.hu/">webcams</a> |
		<a href="https://market.android.com/details?id=com.nonoo.homersekletwidget.tata">android widget</a>
	</div>

	<h1><?php echo $contextTitle; ?></h1>
	<?php displayContext($context, $interval); ?>
</body>
</html>
