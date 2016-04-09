<?php

$distanceInMinuteLimit = 30;
$distancePerMinute     = 200;

require 'DistanceCalculator.php';
require 'ArrayValuePairGenerator.php';
require 'JCDecauxApiClient.php';

$apiClient = new JCDecauxApiClient();
$stations  = $apiClient->getAllStations();

$stationIds    = array_keys($stations);
$pairGenerator = new ArrayValuePairGenerator();
$stationPairs  = $pairGenerator->getPairs($stationIds);

$distanceCalculator = new DistanceCalculator();

$distancesInTime = [];

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

$isSaved = file_put_contents('edges.json', json_encode($distancesInTime));

if (!$isSaved)
{
	exit('Could not save the file!');
}

var_dump($distancesInTime);
