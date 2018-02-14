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

function show_home()
{
	$content = array();
	
	$total_recipes = (int)get_recipes(null, true);
	$total_tags = (int)db_get_count('tags');
	$total_users = (int)db_get_count('users');
	$total_ingredients = (int)db_get_count('ingredients');
	
	$temp = db_get_rows_sql("
		SELECT (SELECT tag FROM tags WHERE tags.id = id_tag) AS tag, id_tag, COUNT(id_recipe) AS count
		FROM rel_tags_recipes
		GROUP BY id_tag
		ORDER BY count DESC
		LIMIT 1;
	");
	$most_tag_used = "";
	$most_id_tag_used = 0;
	if (!empty($temp)) {
		$most_tag_used = $temp[0]['tag'];
		$most_id_tag_used = $temp[0]['id_tag'];
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT ingredient FROM ingredients WHERE ingredients.id = id_ingredient) AS ingredient,
			id_ingredient,
			COUNT(id_recipe) AS count
		FROM rel_ingredients_recipes
		GROUP BY id_ingredient
		ORDER BY count DESC
		LIMIT 1;
	");
	$most_id_ingredient_used = 0;
	$most_ingredient_used = "";
	if (!empty($temp)) {
		$most_ingredient_used = $temp[0]['ingredient'];
		$most_id_ingredient_used = $temp[0]['id_ingredient'];
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			SUM(duration) AS total_duration, id_recipe
		FROM steps
		GROUP BY id_recipe
		ORDER BY total_duration DESC
		LIMIT 1;
	");
	$recipe_large_duration = array();
	if (!empty($temp))
	{
		$time = time_array_to_string(seconds_to_time_array($most_recipe_duration = $temp[0]['total_duration']));
		
		$recipe_large_duration[0] = $temp[0]['id_recipe'];
		$recipe_large_duration[1] = $temp[0]['recipe'];
		$recipe_large_duration[2] = $time;
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe, id_recipe,
			SUM(duration) AS total_duration
		FROM steps
		GROUP BY id_recipe
		ORDER BY total_duration ASC
		LIMIT 1;
	");
	$recipe_short_duration = array();
	if (!empty($temp))
	{
		$time = time_array_to_string(seconds_to_time_array($most_recipe_duration = $temp[0]['total_duration']));
		
		$recipe_short_duration = array($temp[0]['id_recipe'], $temp[0]['recipe'], $time);
	}
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe, id_recipe,
			COUNT(id) AS count
		FROM steps
		GROUP BY id_recipe
		ORDER BY count DESC
		LIMIT 1;
	");
	$recipe_most_steps = array();
	if (!empty($temp))
		$recipe_most_steps = array($temp[0]['id_recipe'], $temp[0]['recipe'], $temp[0]['count'] . " steps");
	
	$temp = db_get_rows_sql("
		SELECT (SELECT title FROM recipes WHERE recipes.id = id_recipe) AS recipe,
			COUNT(id) AS count, id_recipe
		FROM steps
		GROUP BY id_recipe
		ORDER BY count ASC
		LIMIT 1;
	");
	$recipe_less_steps = array();
	if (!empty($temp))
		$recipe_less_steps = array($temp[0]['id_recipe'], $temp[0]['recipe'], $temp[0]['count'] . " steps");
	
	
	$temp = db_get_rows_sql("
		SELECT (SELECT user FROM users WHERE users.id = id_user) AS user,
			COUNT(id) AS count, id_user
		FROM recipes
		GROUP BY id_user
		ORDER BY count DESC
		LIMIT 1;
	");
	$user_most_recipes = array();
	if (!empty($temp))
		$user_most_recipes = array($temp[0]['id_user'], $temp[0]['user'], $temp[0]['count'] . " recipes");
	
	ob_start();
	?>
	<div class="panel panel-default">
		<div class="panel-heading">Recipes Book</div>
		<div class="panel-body">
			Save your recipes and organize it. It is easy and useful.
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Recipes</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-10"><strong>Total Recipes:</strong></div>
				<div class="col-md-2"><span class='badge'><?=$total_recipes;?></span></div>
			</div>
			<div class="row">
				<div class="col-md-10"><strong>Total tags:</strong></div>
				<div class="col-md-2"><span class='badge'><?=$total_tags;?></span></div>
			</div>
			<div class="row">
				<div class="col-md-10"><strong>Total users:</strong></div>
				<div class="col-md-2"><span class='badge'><?=$total_users;?></span></div>
			</div>
			<div class="row">
				<div class="col-md-10"><strong>Total ingredients:</strong></div>
				<div class="col-md-2"><span class='badge'><?=$total_ingredients;?></span></div>
			</div>
			<div class="row">
				<div class="col-md-10"><strong>Tag most used:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipes&id_tag=<?=$most_id_tag_used;?>"><?=$most_tag_used;?></a></div>
			</div>
			<div class="row">
				<div class="col-md-10"><strong>Ingredient most used:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipes&id_ingredient=<?=$most_id_ingredient_used;?>"><?=$most_ingredient_used;?></a></div>
			</div>
			<div class="row">
				<div class="col-md-8"><strong>Recipe most large duration:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipe&id=<?=$recipe_large_duration[0];?>"><?=$recipe_large_duration[1];?></a></div>
				<div class="col-md-2"><span class='badge'><?=$recipe_large_duration[2];?></span></div>
			</div>
			<div class="row">
				<div class="col-md-8"><strong>Recipe most short duration:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipe&id=<?=$recipe_short_duration[0];?>"><?=$recipe_short_duration[1];?></a></div>
				<div class="col-md-2"><span class='badge'><?=$recipe_short_duration[2];?></span></div>
			</div>
			<div class="row">
				<div class="col-md-8"><strong>Recipe most steps:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipe&id=<?=$recipe_most_steps[0];?>"><?=$recipe_most_steps[1];?></a></div>
				<div class="col-md-2"><span class='badge'><?=$recipe_most_steps[2];?></span></div>
			</div>
			<div class="row">
				<div class="col-md-8"><strong>Recipe less steps:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipe&id=<?=$recipe_less_steps[0];?>"><?=$recipe_less_steps[1];?></a></div>
				<div class="col-md-2"><span class='badge'><?=$recipe_less_steps[2];?></span></div>
			</div>
			<div class="row">
				<div class="col-md-8"><strong>User with most recipes:</strong></div>
				<div class="col-md-2"><a href="index.php?page=recipes&id_user=<?=$user_most_recipes[0];?>"><?=$user_most_recipes[1];?></a></div>
				<div class="col-md-2"><span class='badge'><?=$user_most_recipes[2];?></span></div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Top 5 Recipes</div>
		<div class="panel-body">
			<?php
			$temp = db_get_rows_sql("
				SELECT
					(SUM(points) / COUNT(id_user)) AS average, id_recipe
				FROM points
				GROUP BY id_recipe
				ORDER BY average DESC
				LIMIT 5;
			");
			if (!empty($temp))
			{
				foreach ($temp as $row)
				{
					$recipe = get_recipe($row['id_recipe']);
					
					?>
					<div class="row">
						<div class="col-md-2">
							<?php
							points_to_stars($recipe['points'], $recipe['count_votes']);
							?>
						</div>
						<div class="col-md-2">
							<a href="index.php?page=recipe&id=<?=$recipe['id'];?>">
								<?=$recipe['title'];?>
							</a>
						</div>
						<div class="col-md-8">
							<?=truncate_string($recipe['description']);?>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Last 5 Recipes</div>
		<div class="panel-body">
			<?php
			$temp = db_get_rows_sql("
				SELECT title, description, id
				FROM recipes
				ORDER BY id DESC
				LIMIT 5;
			");
			if (!empty($temp))
			{
				foreach ($temp as $row)
				{
					?>
					<div class="row">
						<div class="col-md-3">
							<a href="index.php?page=recipe&id=<?=$row['id'];?>">
								<?=$row['title'];?>
							</a>
						</div>
						<div class="col-md-9">
							<?=truncate_string($row['description']);?>
						</div>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
	$content["body"] = ob_get_clean();
	
	page($content);
}
?>
