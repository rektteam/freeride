<?php

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', true);
ini_set('max_execution_time', 99999);
ini_set('memory_limit', '32000M');
error_reporting(E_ALL);

require 'SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

if (
	empty($_REQUEST['currentPositionLat'])
	|| empty($_REQUEST['currentPositionLng'])
	|| empty($_REQUEST['destinationPositionLat'])
	|| empty($_REQUEST['destinationPositionLng'])
) {
	exit(json_encode(['status' => 'ERROR', 'error_code' => 'MISSING_PARAMETER']));
}

$stationFinder = new StationFinder();

$closestStationToDeparture = $stationFinder->closest(
	$stationFinder->whereThereAreAvailableBikes(),
	$_REQUEST['currentPositionLat'],
	$_REQUEST['currentPositionLng']
);

$stationsWhereThereAreAvailableBikeStands = $stationFinder->whereThereAreAvailableBikeStands();

$closestStationToDestination = $stationFinder->closest(
	$stationsWhereThereAreAvailableBikeStands,
	$_REQUEST['destinationPositionLat'],
	$_REQUEST['destinationPositionLng']
);

$stations = $stationsWhereThereAreAvailableBikeStands;

// If the departure station has no available bike stands, we have to add it to the station list manually,
// because we need to generate the edges for this station too.
if (empty($stations[$closestStationToDeparture['number']]))
{
	$stations[$closestStationToDeparture['number']] = $closestStationToDeparture;
}

$graph = [];

foreach ($stations as $stationId => $station)
{
	$graph[$stationId] = [];
}

$edges = json_decode(file_get_contents('edges.json'), true);

$i = 0;
foreach ($edges as $stationPair => $distanceInTime)
{
	list($stationAId, $stationBId) = explode('-', $stationPair);

	if (!isset($graph[$stationAId]) || !isset($graph[$stationBId]))
	{
		continue;
	}

	/** @var $stationA Vertex */
	$stationA = $graph[$stationAId];
	$stationB = $graph[$stationBId];

	$i++;
	$graph[$stationAId][$stationBId] = (int)ceil($distanceInTime);
}
//var_dump($graph);
//echo $i;
//exit;

$response = [
	'status' => 'OK',
	'stations' => [],
];

try
{
	$g = new Dijkstra2($graph);

	$results = $g->shortestPath($closestStationToDeparture['number'], $closestStationToDestination['number']);

	foreach ($results as $stationId)
	{
		$response['stations'][] = [
			'lat' => $stations[$stationId]['position']['lat'],
			'lng' => $stations[$stationId]['position']['lng'],
		];
	}
}
catch (Exception $exception)
{
}

echo json_encode($response);