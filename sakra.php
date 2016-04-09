<?php
header('Content-type: text/html; charset=utf-8');

ini_set('display_errors', true);
error_reporting(E_ALL);

require 'SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

$apiClient = new StationFinder();
$stations  = $apiClient->all();

$stationIds    = array_keys($stations);
$pairGenerator = new ArrayValuePairGenerator();
$stationPairs  = $pairGenerator->getPairs($stationIds);



var_dump($stationPairs);
exit;
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