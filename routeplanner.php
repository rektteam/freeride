<?php

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', true);
error_reporting(E_ALL);

require 'SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

use ShortestPath\Graph\Graph;
use ShortestPath\Graph\Vertex;
use ShortestPath\Graph\Algorithm\Dijkstra;

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

$vertexes = [];

foreach ($stations as $stationId => $station)
{
	$vertexes[$stationId] = new Vertex($stationId);
}

$edges = json_decode(file_get_contents('edges.json'), true);

foreach ($edges as $stationPair => $distanceInTime)
{
	list($stationAId, $stationBId) = explode('-', $stationPair);

	if (!isset($vertexes[$stationAId]) || !isset($vertexes[$stationBId]))
	{
		continue;
	}

	/** @var $stationA Vertex */
	$stationA = $vertexes[$stationAId];
	$stationB = $vertexes[$stationBId];

	$stationA->connect($stationB, (int)ceil($distanceInTime));
}

$graph = new Graph();

foreach ($vertexes as $vertex)
{
	$graph->add($vertex);
}

$dijkstra = new Dijkstra($graph);
$dijkstra->setStartingVertex($vertexes[50]);
//$dijkstra->setStartingVertex($vertexes[$closestStationToDeparture['number']]);
//$dijkstra->setEndingVertex($vertexes[$closestStationToDestination['number']]);
$dijkstra->setEndingVertex($vertexes[40]);

$response = [
	'status' => 'OK',
	'stations' => [],
];

try
{
	$dijkstra->solve();
	$results = $dijkstra->getShortestPath();

	/** @var $results Vertex[] */
	foreach ($results as $result)
	{
		$stationId = $result->getId();

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