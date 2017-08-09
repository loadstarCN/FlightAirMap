<?php
require_once('require/class.Connection.php');
require_once('require/class.Spotter.php');
require_once('require/class.Language.php');
if (!isset($_GET['pilot'])) {
        header('Location: '.$globalURL.'/pilot');
        die();
}
$Spotter = new Spotter();
$sort = filter_input(INPUT_GET,'sort',FILTER_SANITIZE_STRING);
$pilot = filter_input(INPUT_GET,'pilot',FILTER_SANITIZE_STRING);
$year = filter_input(INPUT_GET,'year',FILTER_SANITIZE_NUMBER_INT);
$month = filter_input(INPUT_GET,'month',FILTER_SANITIZE_NUMBER_INT);
$filter = array();
if ($year != '') $filter = array_merge($filter,array('year' => $year));
if ($month != '') $filter = array_merge($filter,array('month' => $month));
$spotter_array = $Spotter->getSpotterDataByPilot($pilot,"0,1", $sort,$filter);

if (!empty($spotter_array))
{
	$title = sprintf(_("Most Common Time of Day of %s"),$spotter_array[0]['pilot_name']);
	require_once('header.php');
	print '<div class="info column">';
	print '<h1>'.$spotter_array[0]['pilot_name'].'</h1>';
//	print '<div><span class="label">'._("Ident").'</span>'.$spotter_array[0]['ident'].'</div>';
//	print '<div><span class="label">'._("Airline").'</span><a href="'.$globalURL.'/airline/'.$spotter_array[0]['airline_icao'].'">'.$spotter_array[0]['airline_name'].'</a></div>'; 
	print '</div>';

	include('pilot-sub-menu.php');
	print '<div class="column">';
	print '<h2>'._("Most Common Time of Day").'</h2>';
	print '<p>'.sprintf(_("The statistic below shows the most common time of day of flights piloted by <strong>%s</strong>."),$spotter_array[0]['pilot_name']).'</p>';

	$hour_array = $Spotter->countAllHoursByPilot($pilot,$filter);
	print '<link href="'.$globalURL.'/css/c3.min.css" rel="stylesheet" type="text/css">';
	print '<script type="text/javascript" src="'.$globalURL.'/js/d3.min.js"></script>';
	print '<script type="text/javascript" src="'.$globalURL.'/js/c3.min.js"></script>';
	print '<div id="chartHour" class="chart" width="100%"></div><script>';
	$hour_data = '';
	$hour_cnt = '';
	$last = 0;
	foreach($hour_array as $hour_item)
	{
		while($last != $hour_item['hour_name']) {
			$hour_data .= '"'.$last.':00",';
			$hour_cnt .= '0,';
			$last++;
		}
		$last++;
		$hour_data .= '"'.$hour_item['hour_name'].':00",';
		$hour_cnt .= $hour_item['hour_count'].',';
	}
	$hour_data = "['x',".substr($hour_data, 0, -1)."]";
	$hour_cnt = "['flights',".substr($hour_cnt,0,-1)."]";
	print 'c3.generate({
	    bindto: "#chartHour",
	    data: {
		x : "x",
		xFormat: "%H:%M",
		columns: ['.$hour_cnt.','.$hour_data.'], types: { flights: "area"}, colors: { flights: "#1a3151"}
	    },
	    axis: { 
		x: { type: "timeseries", tick: { format: "%H:%M" }},
		y: { label: "# of Flights",tick: { format: d3.format("d") }}
	    },
	    legend: { show: false }
	});';
	print '</script>';
	if (!empty($hour_array))
	{
		print '<div class="table-responsive">';
		print '<table class="common-hour table-striped">';
		print '<thead>';
		print '<th>'._("Hour").'</th>';
		print '<th>'._("Number").'</th>';
		print '</thead>';
		print '<tbody>';
		$i = 1;
		foreach($hour_array as $hour_item)
		{
			print '<tr>';
			print '<td>'.$hour_item['hour_name'].':00</td>';
			print '<td>'.$hour_item['hour_count'].'</td>';
			print '</tr>';
			$i++;
		}
		print '<tbody>';
		print '</table>';
		print '</div>';
	}
	print '</div>';
} else {
	$title = _("Pilot");
	require_once('header.php');
	print '<h1>'._("Error").'</h1>';
	print '<p>'._("Sorry, this pilot is not in the database. :(").'</p>';
}

require_once('footer.php');
?>