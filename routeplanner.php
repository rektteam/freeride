<?php

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', true);
error_reporting(E_ALL);

require __DIR__ . '/source/php/ClassAutoloader.php';
$classLoader = new ClassAutoloader();
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

foreach ($edges as $stationPair => $distanceInTime)
{
	list($stationAId, $stationBId) = explode('-', $stationPair);

	if (!isset($graph[$stationAId]) || !isset($graph[$stationBId]))
	{
		continue;
	}

	$stationA = $graph[$stationAId];
	$stationB = $graph[$stationBId];

	$graph[$stationAId][$stationBId] = (int)ceil($distanceInTime);
}

$response = [
	'status' => 'OK',
	'stations' => [],
];

try
{
	$dijkstra = new Dijkstra($graph);

	$path = $dijkstra->getShortestPath($closestStationToDeparture['number'], $closestStationToDestination['number']);

	foreach ($path as $stationId)
	{
		$response['stations'][] = [
			'lat' => $stations[$stationId]['position']['lat'],
			'lng' => $stations[$stationId]['position']['lng'],
		];
	}
}
catch (Exception $exception)
{
	// In this case the station list is empty.
}

echo json_encode($response);