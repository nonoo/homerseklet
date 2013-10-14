<?php
	function getContexts() {
		global $db, $config;

		$query = "show tables from `${config['dbName']}`";
		if (($res = $db->query($query)) === FALSE)
			echo "error executing db query: $query\nerror: " . $db->error . "\n";

		$contexts = array();
		while ($context = $res->fetch_array(MYSQLI_NUM)) {
			$context = substr($context[0], 0, strpos($context[0], '-'));
			if (array_search($context, $contexts) === FALSE)
				$contexts[] = $context;
		}

		return $contexts;
	}

	function getSubcontexts($context) {
		global $db, $config;

		$query = "show tables from `${config['dbName']}` where `Tables_in_${config['dbName']}` like '" .
			$db->escape_string($context) . "-%'";
		if (($res = $db->query($query)) === FALSE)
			echo "error executing db query: $query\nerror: " . $db->error . "\n";

		$subcontexts = array();
		while ($subcontext = $res->fetch_row())
			$subcontexts[] = $subcontext[0];
		return $subcontexts;
	}

	function getSubcontextTitle($subcontext) {
		return ucwords(substr($subcontext, strpos($subcontext, '-')+1));
	}

	function getContextName($context) {
		switch ($context) {
			case 'tata': return 'Tata';
			case 'bp': return 'Budapest';
			default: return 'Unknown';
		}
	}

	function getIntervalName($interval) {
		switch ($interval) {
			case '3d': return '3 days';
			case '1w': return '1 week';
			case '1m': return '1 month';
			case '3m': return '3 months';
			case '1y': return '1 year';
			case '5y': return '5 years';
			default: return 'Unknown';
		}
	}

	function displayContext($context, $interval) {
		global $db, $config;

		$subcontexts = getSubcontexts($context);
		for ($i = 0; $i < count($subcontexts); $i++) {
			$subcontextTitle = getSubcontextTitle($subcontexts[$i]);
?>
		<div class="stat">
			<div class="actualtemperature">
				<span class="bgtext"><?php echo $subcontextTitle; ?></span>
				<span class="contextname"><?php echo $subcontextTitle; ?></span> actual temperature:
<?php
			$query = "select * from `$subcontexts[$i]` order by `date` desc limit 1";
			if (($res = $db->query($query)) === FALSE)
				echo "error executing db query: $query\nerror: " . $db->error . "\n";
			$res = $res->fetch_object();
?>
				<span id="<?php echo $context; ?>-actualtemperature-value" class="temperaturevalue"><?php echo $res->value; ?>°C</span>
				<span id="<?php echo $context; ?>-actualtemperature-lastrefresh" class="date<?php if (time()-strtotime($res->date) > $config['maxSecsBetweenPostTemps']) echo ' error'; ?>"><?php echo $res->date; ?></span>
			</div>

			<div class="minmaxtemperature">
				Absolute minimum:
<?php
			$query = "select * from `$subcontexts[$i]` where `value` = (select min(`value`) from `$subcontexts[$i]`)";
			if (($res = $db->query($query)) === FALSE)
				echo "error executing db query: $query\nerror: " . $db->error . "\n";
			$res = $res->fetch_object();
?>
					<span id="<?php echo $context; ?>-mintemperature-value" class="mintemperaturevalue"><?php echo $res->value; ?>°C</span>
					<span id="<?php echo $context; ?>-mintemperature-date" class="date"><?php echo $res->date; ?></span><br/>
				Absolute maximum:
<?php
			$query = "select * from `$subcontexts[$i]` where `value` = (select max(`value`) from `$subcontexts[$i]`)";
			if (($res = $db->query($query)) === FALSE)
				echo "error executing db query: $query\nerror: " . $db->error . "\n";
			$res = $res->fetch_object();
?>
					<span id="<?php echo $context; ?>-maxtemperature-value" class="maxtemperaturevalue"><?php echo $res->value; ?>°C</span>
					<span id="<?php echo $context; ?>-maxtemperature-date" class="date"><?php echo $res->date; ?></span>
			</div>
		</div> <!-- div:stat -->
<?php
	}
?>

	<div id="<?php echo $context; ?>-interval" class="intervalselector">
		Interval:
			<input type="button" value="<?php echo getIntervalName('3d'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=3d';"<?php if ($interval == '3d') echo ' class="selected"'; ?> />
			<input type="button" value="<?php echo getIntervalName('1w'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=1w';"<?php if ($interval == '1w') echo ' class="selected"'; ?> />
			<input type="button" value="<?php echo getIntervalName('1m'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=1m';"<?php if ($interval == '1m') echo ' class="selected"'; ?> />
			<input type="button" value="<?php echo getIntervalName('3m'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=3m';"<?php if ($interval == '3m') echo ' class="selected"'; ?> />
			<input type="button" value="<?php echo getIntervalName('1y'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=1y';"<?php if ($interval == '1y') echo ' class="selected"'; ?> />
			<input type="button" value="<?php echo getIntervalName('5y'); ?>" onclick="location.href='?context=<?php echo $context; ?>&interval=5y';"<?php if ($interval == '5y') echo ' class="selected"'; ?> />
	</div>
	<div style="clear: both;"></div>

	<div class="graphcontainer">
		<div id="graph-<?php echo $context; ?>" class="graph"></div>
		<div id="graphloading-<?php echo $context; ?>" class="graphloading"><img src="ajax-loader.gif" /></div>
		<script type="text/javascript">
			g = new Dygraph(
				// containing div
				document.getElementById("graph-<?php echo $context; ?>"),
				"gettemp.csv.php?context=<?php echo $context; ?>&interval=<?php echo $interval; ?>",
				{
					title: "<?php echo getContextName($context); ?>",
					connectSeparatedPoints: true,
					ylabel: 'Temperature (°C)',
					showRoller: true,
					rollPeriod: 2,
					legend: 'always',
					labelsDivStyles: { 'position': 'absolute' },
					labelsDivWidth: 320,
					showRangeSelector: true,
					drawCallback: function() {
						document.getElementById('graphloading-<?php echo $context; ?>').style.display = 'none';
					}
				}
			);
		</script>
	</div>
<?php
	}
?>
