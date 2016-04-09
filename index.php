<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Freeride App</title>
		<meta name="description" content="Freeride App">
		<meta name="author" content="SitePoint">
		<link href='https://fonts.googleapis.com/css?family=Roboto:400,700,900,300' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href='./public/simple.min.css'/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<meta name="apple-mobile-web-app-capable" content="yes">
	</head>
	<body id="page-top" class="index">
		<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
            <div class="loader"><i class="fa fa-cog fa-spin"></i></div>
			<div class="navbar-header page-scroll">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">Freeride</a>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li class="hidden">
						<a href="/"></a>
					</li>
					<li class="page-scroll">
						<a class="get-closest" href="#">Closest Bike</a>
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