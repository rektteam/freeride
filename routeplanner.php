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
	|| empty($_REQUEST['currentPositionLon'])
	|| empty($_REQUEST['destinationPositionLat'])
	|| empty($_REQUEST['destinationPositionLon'])
) {
	exit(json_encode(['status' => 'ERROR', 'error_code' => 'MISSING_PARAMETER']));
}

$stationFinder = new StationFinder();

$closestStationToDeparture = $stationFinder->closest(
	$stationFinder->whereThereAreAvailableBikes(),
	$_REQUEST['currentPositionLat'],
	$_REQUEST['currentPositionLon']
);

$stationsWhereThereAreAvailableBikeStands = $stationFinder->whereThereAreAvailableBikeStands();

$closestStationToDestination = $stationFinder->closest(
	$stationsWhereThereAreAvailableBikeStands,
	$_REQUEST['destinationPositionLat'],
	$_REQUEST['destinationPositionLon']
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

$keys = array_flip(array_keys($vertexes));

foreach ($keys as $key => $value)
{
	$keys[$key] = 0;
}

//var_dump($closestStationToDeparture);
//var_dump($closestStationToDestination);

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

	$keys[$stationAId] += 1;
	$keys[$stationBId] += 1;
}

$graph = new Graph();

foreach ($vertexes as $vertex)
{
	$graph->add($vertex);
}

$dijkstra = new Dijkstra($graph);
$dijkstra->setStartingVertex($vertexes[$closestStationToDeparture['number']]);
$dijkstra->setEndingVertex($vertexes[$closestStationToDestination['number']]);

echo $dijkstra->getLiteralShortestPath() . PHP_EOL;
echo 'Distance: ' . $dijkstra->getDistance();