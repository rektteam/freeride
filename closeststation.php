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
) {
    exit(json_encode(['status' => 'ERROR', 'error_code' => 'MISSING_PARAMETER']));
}

$stationFinder = new StationFinder();

$closestStationToDeparture = $stationFinder->closest(
    $stationFinder->whereThereAreAvailableBikes(),
    $_REQUEST['currentPositionLat'],
    $_REQUEST['currentPositionLng']
);

echo json_encode([
    'status' => 'OK',
    'stations' => $closestStationToDeparture,
]);
