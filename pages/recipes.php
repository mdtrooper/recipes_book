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

function show_recipes()
{
	global $config;
	
	$content = array();
	$content["title"] = "Recipes book - Recipes";
	$content["section"] = "recipes";
	
	// The parameters
	$free_search = (string)get_parameter('free_search', '');
	
	$conditions = array();
	$conditions['free_search'] = $free_search;
	
	$count_recipes = get_recipes_extended($conditions, true); debug($count_recipes, true);
	$pagination_values = pagination_get_values($count_recipes);
	$recipes = get_recipes_extended($conditions, false, $pagination_values);
	
	ob_start();
	?>
	<form id="filter_form" method="post" action="index.php">
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title row">
						<div class="col-md-11">
							<a data-toggle="collapse" type="button" class="btn btn-default btn-lg btn-block" href="#form_filter">Filters <span class="glyphicon glyphicon-filter"></span></a>
						</div>
						<div class="col-md-1">
							<a type="button" class="btn btn-default btn-lg btn-block"><span class="glyphicon glyphicon-search"></a>
						</div>
					</h4>
				</div>
				<div id="form_filter" class="panel-collapse collapse">
					<div class="panel-body">
						<input type="text" class="form-control" placeholder="Free search" name="free_search" value="<?=$free_search;?>">
						
						<div class="input-group">
							<span class="input-group-addon">Tags</span>
							<input name="tags" class="form-control" type="text" value="<?=$tags;?>" />
						</div>
						
						<div class="input-group">
							<span class="input-group-addon">Ingredients</span>
							<input name="ingredients" class="form-control" type="text" value="<?=$ingredients;?>" />
						</div>
						
						<div class="panel panel-default">
							<div class="panel-heading">Duration</div>
							<div class="row">
								<div class="col-md-3">
									<input class="form-control" type="text" name="duration_hours" value="<?=$duration_hours;?>">
								</div>
								<div class="col-md-3">
									<input class="form-control" type="text" name="duration_minutes" value="<?=$duration_minutes;?>">
								</div>
								<div class="col-md-3">
									<input class="form-control" type="text" name="duration_seconds" value="<?=$duration_seconds;?>">
								</div>
								<div class="col-md-3">
									<label class="btn-default btn-block">
										<input type="checkbox" autocomplete="off" name="enable_duration"> Enable
									</label>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-9 ">
								<div class="input-group">
									<span class="input-group-addon">Servings</span>
									<input class="form-control" type="text" name="servings" value="<?=$servings;?>">
								</div>
							</div>
							<div class="col-md-3">
								<label class="btn-default btn-block">
									<input type="checkbox" autocomplete="off" name="enable_servings"> Enable
								</label>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-9 ">
								<div class="input-group">
									<span class="input-group-addon">Steps</span>
									<input class="form-control" type="text" name="steps" value="<?=$steps;?>">
								</div>
							</div>
							<div class="col-md-3">
								<label class="btn-default btn-block">
									<input type="checkbox" autocomplete="off" name="enable_steps"> Enable
								</label>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-9 ">
								<div class="input-group">
									<span class="input-group-addon">Votes</span>
									<input class="form-control" type="text" name="votes" value="<?=$votes;?>">
								</div>
							</div>
							<div class="col-md-3">
								<label class="btn-default btn-block">
									<input type="checkbox" autocomplete="off" name="enable_steps"> Enable
								</label>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</form>
	<div class="panel panel-default">
		<?php
		print_pagination($pagination_values, "index.php?page=recipes");
		?>
		<div class="list-group">
			<?php
			foreach ($recipes as $recipe)
			{
			?>
				<div class="list-group-item">
					<div class="input-group-btn" >
						<a class="btn btn-default col-md-12" style="text-align: left;" href="index.php?page=show_recipe&id_recipe=<?=$recipe['id'];?>">
							<?php
							echo $recipe['title'] . " - " . truncate_string($recipe['description']);
							?>
						</a>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
	<?php
	
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>