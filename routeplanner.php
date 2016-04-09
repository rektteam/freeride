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
	empty($_POST['currentPositionLat'])
	|| empty($_POST['currentPositionLon'])
	|| empty($_POST['destinationPositionLat'])
	|| empty($_POST['destinationPositionLon'])
) {
	exit(json_encode(['status' => 'ERROR', 'error_code' => 'MISSING_PARAMETER']));
}
else
{
	exit(
		json_encode(
			[
				'status' => 'OK',
				'stations' => [
					['name' => 'FOIRE', 'lat' => 49.63706, 'lon' => 6.17044],
					['name' => 'MUGUETS', 'lat' => 49.6216, 'lon' => 6.1565],
					['name' => 'GARE CENTRALE', 'lat' => 49.6002, 'lon' => 6.1336],
					['name' => 'SCHAARFEN ECK', 'lat' => 49.58043, 'lon' => 6.11465],
				]
			]
		)
	);
}

$stationFinder = new StationFinder();
$stations = $stationFinder->whereThereAreFreeBikes();

$vertexes = [];

foreach ($stations as $stationId => $station)
{
	$vertexes[$stationId] = new Vertex($stationId);
}

$edges = json_decode(file_get_contents('edges.json'), true);

foreach ($edges as $stationPair => $distanceInTime)
{
	list($stationAId, $stationBId) = explode('-', $stationPair);

	if (!isset($vertexes[$stationAId], $vertexes[$stationBId]))
	{
		continue;
	}

	/** @var $stationA Vertex */
	$stationA = $vertexes[$stationAId];
	$stationB = $vertexes[$stationBId];

	$stationA->connect($stationB, intval($distanceInTime));
}

$graph = new Graph();

foreach ($vertexes as $vertex)
{
	$graph->add($vertex);
}

$dijkstra = new Dijkstra($graph);
$dijkstra->setStartingVertex($vertexes['62']);
$dijkstra->setEndingVertex($vertexes['55']);

echo $dijkstra->getLiteralShortestPath() . PHP_EOL;
echo 'Distance: ' . $dijkstra->getDistance();