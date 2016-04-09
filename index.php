<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require 'SplClassLoader.php';

$classLoader = new SplClassLoader();
$classLoader->register();

use ShortestPath\Graph\Graph;
use ShortestPath\Graph\Vertex;
use ShortestPath\Graph\Algorithm\Dijkstra;

$graph = new Graph();

$apiClient = new JCDecauxApiClient();
$stations  = $apiClient->getAllStations();

$vertexes = [];

foreach ($stations as $stationId => $station)
{
	$vertexes[$stationId] = new Vertex($stationId);
}

$edges = json_decode(file_get_contents('edges.json'), true);

foreach ($edges as $stationPair => $distanceInTime)
{
	list($stationAId, $stationBId) = explode('-', $stationPair);

	/** @var $stationA Vertex */
	$stationA = $vertexes[$stationAId];
	$stationB = $vertexes[$stationBId];

	$stationA->connect($stationB, $distanceInTime);
}

foreach ($vertexes as $vertex)
{
	$graph->add($vertex);
}

$dijkstra = new Dijkstra($graph);
$dijkstra->setStartingVertex($vertexes['62']);
$dijkstra->setEndingVertex($vertexes['55']);
$dijkstra->solve();
//echo $dijkstra->getLiteralShortestPath() . PHP_EOL;
//echo 'Distance: ' . $dijkstra->getDistance();
exit;
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>The HTML5 Herald</title>
		<meta name="description" content="The HTML5 Herald">
		<meta name="author" content="SitePoint">
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,900,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href='./public/simple.min.css'/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="apple-mobile-web-app-capable" content="yes">
	</head>
	<body id="page-top" class="index">
		<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header page-scroll">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#page-top">Team Rekt</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden">
						<a href="#page-top"></a>
					</li>
					<li class="page-scroll">
						<a href="#">Portfolio</a>
					</li>
					<li class="page-scroll">
						<a href="#">About</a>
					</li>
					<li class="page-scroll">
						<a href="#">Contact</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
		<header>
			<div class="container">
				<div class="row">
					<div id="google-maps">
						<i class="fa fa-refresh fa-spin post-loading"></i>
					</div>
				</div>
			</div>
		</header>
	<script src="./public/simple.js"></script>
	<script type="text/javascript"
			src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBe684glxJ4Xv1Iy91Amy-Og_H7bJwakMU">
	</script>
	</body>
</html>