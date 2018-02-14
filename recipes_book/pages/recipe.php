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
						<a href="index.php?page=recipes&duration=<?=$recipe['duration'];?>">
						<span class='badge'>
							<h3><?=time_array_to_string(seconds_to_time_array($recipe['duration']));?></h3>
						</a>
					</div>
					<div class="col-md-1">
						<a href="index.php?page=recipes&servings=<?=$recipe['servings'];?>">
						<span class='badge'>
							<h3>
								<?=$recipe['servings'];?>
								<span class="glyphicon glyphicon-cutlery" aria-hidden="true"></span>
							</h3>
						</a>
					</div>
					<div class="col-md-2">
						<div>
							<span id="points">
								<?php
								points_to_stars($recipe['points'], $recipe['count_votes']);
								?>
							</span>
							<span id="votation_points" style="display: none;">
								<input class="form-control" type="text" value="<?=get_votes_user($id);?>" size="1" name="vote_points">
							</span>
						</div>
						<div>
							<span class="btn-group" role="group">
								<a href="#" onclick="show_votation_input();" class="btn btn-default">
									<span id="vote_button" class="glyphicon glyphicon-flash" aria-hidden="true"></span>
									<span id="back_vote_button" class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="display:none;"></span>
								</a>
								<a href="index.php?page=recipes&points=<?=round($recipe['points'], 2);?>" class="btn btn-default">
									<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
								</a>
							</span>
						</div>
					</div>
					<div class="col-md-6 text-uppercase">
						<h3><?=$recipe['title'];?></h3>
					</div>
					<div class="col-md-1">
						<a href="index.php?page=recipes&steps=<?=$recipe['steps'];?>">
							<span class='badge'>
								<h3><?=count($recipe['steps']) . " steps";?></h3>
							</span>
						</a>
					</div>
					<div class="col-md-1">
						<a href="index.php?page=recipe_form&action=edit_recipe&id_recipe=<?=$id;?>" class="btn btn-default btn-lg" aria-label="Left Align">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
						</a>
					</div>
				<?php
				}
				else
				{
				?>
					<div class="col-md-1">
						<a href="index.php?page=recipes&duration=<?=$recipe['duration'];?>">
						<span class='badge'>
							<h3><?=time_array_to_string(seconds_to_time_array($recipe['duration']));?></h3>
						</span>
						</a>
					</div>
					<div class="col-md-1">
						<a href="index.php?page=recipes&servings=<?=$recipe['servings'];?>">
						<span class='badge'>
							<h3>
								<?=$recipe['servings'];?>
								<span class="glyphicon glyphicon-cutlery" aria-hidden="true"></span>
							</h3>
							
						</a>
					</div>
					<div class="col-md-2">
						<?php
						points_to_stars($recipe['points'], $recipe['count_votes']);
						?>
						<a href="index.php?page=recipes&points=<?=$recipe['points'];?>" class="btn btn-default">
							<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
						</a>
					</div>
					<div class="col-md-7 text-uppercase">
						<h3><?=$recipe['title'];?></h3>
					</div>
					<div class="col-md-1">
						<a href="index.php?page=recipes&steps=<?=$recipe['steps'];?>">
							<span class='badge'>
								<h3><?=count($recipe['steps']) . " steps";?></h3>
							</span>
						</a>
					</div>
				<?php
				}
				?>
			</div>
			<div class="panel-body">
				<?=$recipe['description'];?>
			</div>
		</div>
		
		
		<table class="table table-striped table-hover">
			<caption class="row">
				<div class="col-md-9">
					<strong>TAGS</strong>
				</div>
				<div class="col-md-3">
					<a href="index.php?page=recipes&similar=1&type=tags&id_recipe=<?=$id;?>" class="btn btn-default btn-mg" aria-label="Left Align">
						Search similar recipes (in tags) 
						<span class="glyphicon  glyphicon glyphicon-search" aria-hidden="true"></span>
					</a>
				</div>
			</caption>
			<tr>
				<td>
					<?php
					$first = true;
					foreach ($recipe['tags'] as $tag)
					{
						if (!$first)
							echo " , ";
						$first = false;
						
						?>
						<a href="index.php?page=recipes&id_tag=<?=$tag['id_tag'];?>"><?=$tag['tag'];?></a>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
		
		<?php
		if (!empty($recipe['ingredients']))
		{
			?>
			<table class="table table-striped table-hover">
				<caption class="row">
					<div class="col-md-9">
						<strong>INGREDIENTS</strong>
					</div>
					<div class="col-md-3">
						<a href="index.php?page=recipes&similar=1&type=ingredients&id_recipe=<?=$id;?>" class="btn btn-default btn-mg" aria-label="Left Align">
							Search similar recipes (in ingredients) 
							<span class="glyphicon  glyphicon glyphicon-search" aria-hidden="true"></span>
						</a>
					</div>
				</caption>
				<tbody>
					<?php
					foreach ($recipe['ingredients'] as $ingredient)
					{
					?>
					<tr class="row">
						<td class="col-md-1">
							<span class='badge'>
								<?=$ingredient['amount'] . " " . $ingredient['measure_type'];?>
							</span>
						</td>
						<td class="col-md-3">
							<a href="index.php?page=recipes&id_ingredient=<?=$ingredient['id_ingredient'];?>">
							<?=$ingredient['ingredient'];?>
							</a>
						</td>
						<td class="col-md-8">
							<?=$ingredient['notes'];?>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
		
		if (!empty($recipe['steps']))
		{
			?>
			<table class="table table-striped table-hover">
				<caption>
					<strong>STEPS</strong>
				</caption>
				<tbody>
					<?php
					foreach ($recipe['steps'] as $step)
					{
					?>
					<tr class="row">
						<td class="col-md-1">
							<strong>
								<?=$step['position'];?>º
							</strong>
						</td>
						<td class="col-md-1">
							<span class='badge'>
								<?=time_array_to_string(seconds_to_time_array($step['duration']));?>
							</span>
						</td>
						<td class="col-md-10">
							<?=$step['step'];?>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<script type="text/javascript" language="javascript">
				function show_votation_input()
				{
					$("#points").toggle();
					$("#votation_points").toggle();
					
					$("#vote_button").toggle();
					$("#back_vote_button").toggle();
				}
				
				function refresh_stars(stars)
				{
					$("#stars").html(stars);
				}
				
				$(
					function()
					{
						$("input[name='vote_points']").TouchSpin
						(
							{
								"min": 0,
								"max": 5
							}
						)
						.on
						(
							"change", function(i,e)
							{
								var points = $("input[name='vote_points']").val();
								$.ajax
								(
									{
										url: 'index.php?ajax=1&action=vote_user&points=' + points + '&id_recipe=' + <?=$id;?>,
										type: 'GET',
										dataType: 'json',
										error:
											function()
											{
												// None
											},
										success:
											function(data)
											{
												refresh_stars(data['stars'], data['count_votes']);
											}
									}
								);
							}
						)
					}
				);
			</script>
			<?php
		}
		
	}
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>