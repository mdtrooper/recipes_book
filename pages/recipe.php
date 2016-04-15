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

function show_recipe()
{
	global $config;
	
	$id = (int)get_parameter('id');
	
	$recipe = get_recipe($id);
	
	$content = array();
	if (empty($recipe))
	{
		$content["title"] = "Recipes book - Recipe - ERROR: NOT FOUND RECIPE";
	}
	else {
		$content["title"] = "Recipes book - Recipe - " . $recipe['title'];
	}
	$content["section"] = "recipe";
	
	
	
	ob_start();
	if (empty($recipe))
	{
		?>
		<div class="alert alert-danger" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<strong>Error!</strong> Not found recipe.
		</div>
		<?php
	}
	else
	{
	?>
	<div class="panel panel-default">
		<div class="panel-heading row">
			<?php
			if ($config['id_user'] == $recipe['id_user'])
			{
			?>
			<div class="col-md-1">
				<span class='badge'>
					<h3><?=time_array_to_string(seconds_to_time_array($recipe['duration']));?></h3>
				</span>
			</div>
			<div class="col-md-9 text-uppercase">
				<h3><?=$recipe['title'];?></h3>
			</div>
			<div class="col-md-1">
				<span class='badge'>
					<h3><?=count($recipe['steps']) . " steps";?></h3>
				</span>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-default btn-lg" aria-label="Left Align">
					<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
				</button>
			</div>
			<?php
			}
			else
			{
			?>
			<h3><?=$recipe['title'];?></h3>
			<?php
			}
			?>
		</div>
		<div class="panel-body">
			<?=$recipe['description'];?>
		</div>
	</div>
	<?php
	}
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>