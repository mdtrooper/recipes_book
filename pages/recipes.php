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
	$conditions = array();
	
	$flag_filter_enabled = false;
	$free_search = (string)get_parameter('free_search', '');
	if (!empty($free_search))
	{
		$flag_filter_enabled = true;
		$conditions['free_search'] = $free_search;
	}
	
	$tags = "";
	$tags_list = get_parameter('tags', "");
	if (!empty($tags_list))
	{
		$tags .= $tags_list;
		$tags_array = explode(',', $tags_list);
		
		foreach ($tags_array as $tag)
		{
			$flag_filter_enabled = true;
			$id_tag = (int)db_get_value('tags', 'id',
				array('tag' => array('=' => $tag)));
			if (!empty($id_tag))
				$conditions['id_tag'][] = $id_tag;
		}
	}
	
	$id_tag = (int)get_parameter('id_tag');
	if (!empty($id_tag))
	{
		$flag_filter_enabled = true;
		$conditions['id_tag'] = $id_tag;
		
		$tag_row = db_get_value('tags', 'tag',
			array('id' => array('=' => $id_tag)));
		
		$tags .= $tag_row;
	}
	
	if ($flag_filter_enabled)
	{
		$count_recipes = get_recipes_extended($conditions, true);
		$pagination_values = pagination_get_values($count_recipes);
		$recipes = get_recipes_extended($conditions, false, $pagination_values);
	}
	else
	{
		$count_recipes = get_recipes_extended(null, true);
		$pagination_values = pagination_get_values($count_recipes);
		$recipes = get_recipes_extended(null, false, $pagination_values);
	}
	
	ob_start();
	?>
	<form id="filter_form" method="post" action="index.php?page=recipes">
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title row">
						<div class="col-md-11">
							<a data-toggle="collapse" type="button" class="btn btn-default btn-lg btn-block" href="#form_filter">
								<span class="glyphicon glyphicon-filter"></span>
								Filters
								<?php
								if ($flag_filter_enabled)
								{
									?>
									- Some filters applyed
									<?php
								}
								else
								{
									?>
									- Empty filters applyed
									<?php
								}
								?>
							</a>
						</div>
						<div class="col-md-1">
							<a href="javascript: $('#filter_form').submit();" type="button" class="btn btn-default btn-lg btn-block">
								<span class="glyphicon glyphicon-search">
							</a>
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
						<a class="btn btn-default col-md-12" style="text-align: left;" href="index.php?page=recipe&id=<?=$recipe['id'];?>">
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
		<?php
		if ($count_recipes > 0)
			print_pagination($pagination_values, "index.php?page=recipes");
		?>
	</div>
	<script type="text/javascript">
		$("input[name='tags']").tagsinput
		(
			{
				typeahead:
				{
					source:  function(query)
					{
						source = $.getJSON('index.php?ajax=1&action=get_tags&query=' + query);
						
						return source;
					}
				}
			}
		);
	</script>
	<?php
	
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>