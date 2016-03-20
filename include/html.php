<?php
/*
Copyright (C) 2016 Miguel de Dios
--
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
higher any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*/

function page($content = null)
{
	global $config;
	
	if (!isset($content['title']))
		$content['title'] = "Recipes book";
	
	$active = "class='active'";
	$recipes_active =
		$tags_active =
		$recipe_form_active =
		$login_active =
		$user_active =
		$about_active = "";
	if (isset($content["section"]))
	{
		switch ($content["section"])
		{
			case 'about':
				$about_active = $active;
				break;
			case 'login':
				$login_active = $active;
				break;
			case 'recipe_form':
				$recipe_form_active = $active;
				break;
			case 'user':
				$user_active = $active;
				break;
		}
	}
	
	if (!isset($content['body']))
		$content['body'] = "";
	?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			
			<title><?=$content['title'];?></title>
			
			<meta name="description" content="Another Recipes book">
			<meta name="author" content="Miguel de Dios Matias">
			
			<link href="css/bootstrap.min.css" rel="stylesheet">
			<link href="css/jquery.bootstrap-touchspin.css" rel="stylesheet">
			<link href="css/bootstrap-tagsinput.css" rel="stylesheet">
			<link href="css/selectize.css" rel="stylesheet">
			<link href="css/selectize.bootstrap3.css" rel="stylesheet">
			<link href="css/style.css" rel="stylesheet">
			<script src="js/jquery-2.2.0.js"></script>
			<script src="js/bootstrap.min.js"></script>
			<script src="js/jquery.bootstrap-touchspin.min.js"></script>
			<script src="js/bootstrap-tagsinput.js"></script>
			<script src="js/bootstrap3-typeahead.js"></script>
			<script src="js/selectize.js"></script>
			<script src="js/scripts.js"></script>
		</head>
		<body style="padding-top: 70px;">
			<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#toolbar_header">
							<span class="sr-only">Toggle toolbar header</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="index.php">
							<span class="glyphicon glyphicon-cutlery"></span>
							Recipes book
						</a>
					</div>
					
					<div class="collapse navbar-collapse" id="toolbar_header">
						<ul class="nav navbar-nav">
							<li <?=$recipes_active;?>><a href='index.php?page=recipes'>Recipes</a></li>
							<li <?=$tags_active;?>><a href='index.php?page=tags'>Tags</a></li>
							<li <?=$recipe_form_active;?>><a href='index.php?page=recipe_form'>Create recipe</a></li>
							<li <?=$about_active;?>><a href='index.php?page=about'>About</a></li>
						</ul>
						
						<ul class="nav navbar-nav navbar-right">
							<li>
								<form class="navbar-form" role="search">
									<div class="form-group has-feedback">
										<input type="text" class="form-control" placeholder="Search" />
										<i class="glyphicon glyphicon-search form-control-feedback"></i>
									</div>
								</form>
							</li>
							<?php
							if (user_logged())
							{
								?>
								<li <?=$user_active;?>><a href='index.php?page=user'><?=$config['user'];?></a></li>
								<li>
									<a href='index.php?action=logout'>
										<span class="glyphicon glyphicon-log-out"></span>
									</a>
								</li>
								<?php
							}
							else
							{
								?>
								<li <?=$login_active;?>>
									<a href='index.php?page=login'>
										Login
										<span class="glyphicon glyphicon-log-in"></span>
									</a>
								</li>
								<?php
							}
							?>
							
						</ul>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<?=$content['body'];?>
		</body>
	</html>
	<?php
}
?>