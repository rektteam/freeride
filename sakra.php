<?php
header('Content-type: text/html; charset=utf-8');

require 'DistanceCalculator.php';
require 'ArrayValuePairGenerator.php';
require 'JCDecauxApiClient.php';

//var_dump($stations);

//foreach ($stations as $key => $station)
//{
//	if ($station['status'] !== 'OPEN' || empty($station['available_bikes']))
//	{
//		unset($stations[$key]);
//	}
//}

$apiClient = new JCDecauxApiClient();
$stations  = $apiClient->getAllStations();

$stationIds    = array_keys($stations);
$pairGenerator = new ArrayValuePairGenerator();
$stationPairs  = $pairGenerator->getPairs($stationIds);

$distanceCalculator = new DistanceCalculator();

$distancesInTime = [];
$distanceInMinuteLimit = 30;
$distancePerMinute = 200;

foreach ($stationPairs as $stationPair)
{
	$stationAId  = key($stationPair);
	$stationALat = $stations[$stationAId]['position']['lat'];
	$stationALon = $stations[$stationAId]['position']['lng'];

	$stationBId  = current($stationPair);
	$stationBLat = $stations[$stationBId]['position']['lat'];
	$stationBLon = $stations[$stationBId]['position']['lng'];

	$distance = $distanceCalculator->getDistance($stationALat, $stationALon, $stationBLat, $stationBLon);

	$distanceInMinute = $distance / $distancePerMinute;

	if ($distanceInMinute <= $distanceInMinuteLimit)
	{
		$distancesInTime[$stationAId . '-' . $stationBId] = $distanceInMinute;
	}
}

var_dump($distancesInTime);

//$currentPositionLat = 49.6019593;
//$currentPositionLon = 6.1132482;
//
//$destinationPositionLat = 49.6201;
//$destinationPositionLon = 6.1388;
//
//$distance = $distanceCalculator->getDistance(
//	$currentPositionLat,
//	$currentPositionLon,
//	$destinationPositionLat,
//	$destinationPositionLon
//);

//foreach ($stations as $station)
//{
//	echo $station['name'] . PHP_EOL;
//}

//12000 m / 60 p
//200 m / p

//echo 'distance: ' . $distance . ' m - minute: ' . ($distance / 200) . ' m';