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
<!--		<div class="row">-->
<!--			<div class="col-lg-12">-->
<!--				<img data-toggle="modal" data-target="#myModal" class="img-responsive" src="https://avatars0.githubusercontent.com/u/18335366?v=3&s=400" alt="">-->
<!--				<div class="intro-text">-->
					<!--<span class="name">Start get rekt</span>-->
<!--					<hr class="star-light">-->
<!--					<a href="https://github.com/rektteam" target="_blank">-->
<!--						<span class="skills">Follow us on <i class="fa fa-github"></i></span>-->
<!--					</a>-->
<!--					<br/>-->
<!--					<br/>-->
<!--					<button type="button" class="btn btn-primary get-location">-->
<!--						Get my location-->
<!--					</button>-->
<!--					<br/>-->
<!--					<span class="name location-result"></span>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
		<div class="row">
		    <div id="google-maps">
                <i class="fa fa-refresh fa-spin post-loading"></i>
            </div>
        </div>
	</div>
</header>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal Sample</h4>
			</div>
			<div class="modal-body">
				...
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>
<footer>
</footer>
<script src="./public/simple.js"></script>
<script type="text/javascript"
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBe684glxJ4Xv1Iy91Amy-Og_H7bJwakMU">
</script>

</body>
</html>